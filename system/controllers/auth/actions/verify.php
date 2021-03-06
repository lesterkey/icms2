<?php
class actionAuthVerify extends cmsAction {

    public function run($pass_token = null){

        if (empty($this->options['verify_email'])){
            cmsCore::error404();
        }

        if ($this->cms_user->is_logged && !$this->cms_user->is_admin) { $this->redirectToHome(); }

        $users_model = cmsCore::getModel('users');

        $reg_email = cmsUser::getCookie('reg_email');

        if($reg_email && $this->validate_email($reg_email) === true){

            $reg_user = $users_model->getUserByEmail($reg_email);

            $reg_user['resubmit_extime'] = modelAuth::RESUBMIT_TIME - (time() - strtotime($reg_user['date_token']));
            if($reg_user['resubmit_extime'] < 0){
                $reg_user['resubmit_extime'] = 0;
            }

        } else {
            $reg_user = array();
        }

        $form = $this->getForm('verify', array($reg_user));

        $data = array('reg_token' => $pass_token);

        if ($this->request->has('submit')){

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this, $data);

            if (!$errors){

                $user = $users_model->getUserByPassToken($data['reg_token']);

                if (!$user) {
                    $errors['reg_token'] = LANG_VERIFY_EMAIL_ERROR;
                }

            }

            if (!$errors){

                $users_model->unlockUser($user['id'])->clearUserPassToken($user['id']);

                cmsEventsManager::hook('user_registered', $user);

                cmsUser::addSessionMessage($this->options['reg_auto_auth'] ? LANG_REG_SUCCESS_VERIFIED_AND_AUTH : LANG_REG_SUCCESS_VERIFIED, 'success');

                // авторизуем пользователя автоматически
                if ($this->options['reg_auto_auth']){

                    $user = cmsEventsManager::hook('user_login', $user);

                    cmsUser::setUserSession($user);

                    $users_model->updateUserIp($user['id']);

                    cmsEventsManager::hook('auth_login', $user['id']);

                }

                $this->redirect($this->getAuthRedirectUrl($this->options['first_auth_redirect']));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('verify', array(
            'data'   => $data,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
