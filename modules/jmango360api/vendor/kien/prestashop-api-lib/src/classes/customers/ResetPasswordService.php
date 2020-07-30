<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ResetPasswordService extends BaseService
{
    public function doExecute()
    {
        if ($this->isPostMethod()) {
            $request = json_decode($this->getRequestBody());
            $this->response = new JmResponse();
            if (!($email = trim($request->email)) || !Validate::isEmail($email)) {
                if($this->isV17()){
                    $this->response->errors[] = new JmError(500, $this->trans('Invalid email address.', array(), 'Shop.Notifications.Error'));
                } else {
                    $this->response->errors[] = new JmError(500, Tools::displayError('Invalid email address.'));
                }
            } else {
                $customer = new Customer();
                $customer->getByemail($email);
                if (!Validate::isLoadedObject($customer)) {
                    if($this->isV17()){
                        $this->response->errors[] = new JmError(500, $this->trans('There is no account registered for this email address.', array(), 'Shop.Notifications.Error'));
                    } else {
                        $this->response->errors[] = new JmError(500, Tools::displayError('There is no account registered for this email address.'));
                    }
                } elseif (!$customer->active) {
                    if($this->isV17()){
                        $this->response->errors[] = new JmError(500, $this->trans('You cannot regenerate the password for this account.', array(), 'Shop.Notifications.Error'));
                    } else {
                        $this->response->errors[] = new JmError(500, Tools::displayError('You cannot regenerate the password for this account.'));
                    }
                } elseif ((strtotime($customer->last_passwd_gen . '+' . ($min_time = (int)Configuration::get('PS_PASSWD_TIME_FRONT')) . ' minutes') - time()) > 0) {
                    if($this->isV17()){
                        $this->response->errors[] = new JmError(500, $this->trans('You can regenerate your password only every %d minute(s)', array((int) $min_time), 'Shop.Notifications.Error'));
                    } else {
                        $this->response->errors[] = new JmError(500, sprintf(Tools::displayError('You can regenerate your password only every %d minute(s)'), (int)$min_time));
                    }
                } else {
                    $mail_params = array(
                        '{email}' => $customer->email,
                        '{lastname}' => $customer->lastname,
                        '{firstname}' => $customer->firstname,
                        '{url}' => $this->context->link->getPageLink('password', true, null, 'token=' . $customer->secure_key . '&id_customer=' . (int)$customer->id)
                    );
                    if (Mail::Send($this->context->language->id, 'password_query', Mail::l('Password query confirmation'), $mail_params, $customer->email, $customer->firstname . ' ' . $customer->lastname)) {
                    } else {
                        if($this->isV17()){
                            $this->response->errors[] = new JmError(500, $this->trans('An error occurred while sending the email.', array(), 'Shop.Notifications.Error'));
                        } else {
                            $this->response->errors[] = new JmError(500, Tools::displayError('An error occurred while sending the email.'));
                        }
                    }
                }
            }
        }
    }
}
