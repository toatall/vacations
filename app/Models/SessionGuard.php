<?php
namespace App\Models;


class SessionGuard extends \Illuminate\Auth\SessionGuard
{
   
    /**
     * Вход пользователя под window-учетной записью, 
     * если включена Windows-аутентификация
     * {@inheritdoc}
     */
    public function user()
    {        
        $request = $this->getRequest();
        $authUser = $request->server->get('AUTH_USER') 
            ?? $request->server->get('LOGON_USER') 
            ?? $request->server->get('AUTH_USER');
        if (preg_match('/n?\d{4}[-|_].*/', $authUser, $matches)) {            
            if (is_array($matches) && count($matches) >= 1) {
                $userName = $matches[0];      
                $user = User::findOrCreate($userName);  
                if ($user) {                                            
                    return $user;
                }
            }
        }
        return parent::user();
    }

    
}
