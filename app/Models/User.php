<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',    
        'code_org',
        'ad_fio',
        'ad_post',
        'ad_department',
        'ad_memberof',
        'ad_mail',
        'ad_room',
        'ad_disabled',
        'ad_description',
        'last_action',    
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param string $username
     * @return User
     */
    public static function findOrCreate($username)
    {
        $model = User::query()->where('name', $username)->first();
        if ($model === null) {
            $orgCode = self::getOrgByUsername($username);
            $model = new User;
            $model->code_org = $orgCode;                        
        }
        
        if (($ldapData = self::getLdapInfo($username)) !== null) {
            $members = [];
            foreach ((array)$ldapData->getAttribute('memberOf') as $member) {
                $members[] = self::getCN($member);
            }
            $model->name = $username;
            $model->password = md5($model->name);
            $model->email = $ldapData->getAttribute('userPrincipalName')[0] ?? null;
            $model->ad_fio = $ldapData->getAttribute('cn')[0] ?? null;
            $model->ad_post = $ldapData->getAttribute('title')[0] ?? null;
            $model->ad_department = $ldapData->getAttribute('department')[0] ?? null;
            // $model->ad_memberof = implode('#', (array)$ldapData->getAttribute('memberOf')) ?? null;
            $model->ad_memberof = implode(', ', $members) ?? null;
            $model->ad_mail = $ldapData->getAttribute('mail')[0] ?? null;
            $model->ad_room = $ldapData->getAttribute('physicalDeliveryOfficeName')[0] ?? null;
            $model->ad_disabled = false;
            $model->ad_description = $ldapData->getAttribute('info')[0] ?? null;
            $model->last_action = date('d.m.Y H:i:s', time());            
        }
        /** @var User $model */
        if($model->isDirty()) {           
            $model->save();
        }

        return $model;
    }

    /**
     * Получение кода организации по учетной записи
     * @param string $userneme учетная запись (в формате nNNNN... или NNNN...)
     * @return string
     */
    private static function getOrgByUsername($username)
    {
        if (preg_match('/^n{0,1}\d{4}/', $username, $matches)) {
            if ($matches && isset($matches[0])) {
                return $matches[0];
            }
        }
        return '00000';
    }

    /**
     * @param string $username
     * @return \Symfony\Component\Ldap\Entry|null
     */
    private static function getLdapInfo($username)
    {
        $ldap = \Symfony\Component\Ldap\Ldap::create('ext_ldap', [ 
            'host' => env('LDAP_SERVER'),
            'port' => env('LDAP_PORT'),
        ]);                
        $ldap->bind(env('LDAP_BIND_USER'), env('LDAP_BIND_PASSWORD'));        
        $query = $ldap->query(env('LDAP_DN'), '(sAMAccountName='.$username.')');               
        $results = $query->execute()->toArray();
        if (($first = reset($results)) !== false) {
           return $first;
        }
        return null;
    }

    private static function getCN($name)
    {
        if (preg_match('/^CN=([^,]*)/', $name, $matches)) {
            if (is_array($matches) && count($matches) >= 1) {
                return $matches[1];
            }
        }
        return $name;
    }

}

