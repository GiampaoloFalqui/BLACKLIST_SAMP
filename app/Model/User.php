<?php
/**
 * User model.
 *
 * WHITELIST_SAMP: Global Whitelist Platform (https://github.com/GiampaoloFalqui/WHITELIST_SAMP)
 * 
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://github.com/GiampaoloFalqui/WHITELIST_SAMP WHITELIST_SAMP
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

/**
 * User model.
 */
 
class User extends Model {
  
  /**
   * An User has many aliases, if necessary.
   * 
   * @var string
   */
  public $hasMany = 'Alias';
  
  /**
   * An User has only one ApiKey, if registered.
   * 
   * @var string
   */
  public $hasOne = 'ApiKey';
  
  /**
   * Recursion must be minus one so we don't query useless stuff unless we need it.
   * 
   * @var integer
   */    
  public $recursive = -1;
  
  /**
   * User model validations
   * 
   * @var array
   */
  public $validate = array(
    'username' => array(
      'between' => array(
        'rule' => array('between', 4, 20), 'message' => 'The username must be between 4 to 20 characters.'
      ),
      'isUnique' => array(
        'rule' => 'isUnique', 'message' => 'This username has already been used.'
       ),
      'notEmpty' => array(
        'rule' => 'notEmpty', 'message' => 'The username must not be empty.'
       )           
    ),
    'password' => array(
      'between' => array(
        'rule' => array('between', 4, 20), 'message' => 'The password must be between 4 to 20 characters.'
       ),    
      'notEmpty' => array(
        'rule' => 'notEmpty', 'message' => 'The password must not be empty.'
       )
    ),
    'repeat_password' => array(
      'passwordMatch' => array(
        'rule' => 'checkPasswordMatch', 'message' => 'The two passwords must match.'
       ),
      'notEmpty' => array(
        'rule' => 'notEmpty', 'message' => 'The password confirmation must not be empty.'
       )
    ),
    'email' => array(
      'email' => array(
        'rule' => 'email', 'message' => 'The email is invalid.'
       ),
      'isUnique' => array(
        'rule' => 'isUnique', 'message' => 'This email has already been used.'
       ),
      'notEmpty' => array(
        'rule' => 'notEmpty', 'message' => 'The email must not be empty.'
       )
    )
  );
  
  /**
   * Check if the password and repeat_password match.
   * 
   * @return boolean If the two form passwords match, true is returned, otherwise false.
   */
  public function checkPasswordMatch() {
    return $this->data['User']['password'] === $this->data['User']['repeat_password'];
  }
   
  /**
   * beforeSave callback is used in the User model to hash user passwords when registering in Blowfish.
   * Which is why we included BlowfishPasswordHasher earlier, outside of the class.
   */
   
  public function beforeSave($options = array()) {
    $Blowfish = new BlowfishPasswordHasher;
    $this->data['User']['password'] = $Blowfish->hash($this->data['User']['password']);
    $this->data['User']['role'] = 'User';
    $this->data['User']['authy_id'] = -1;
  }  
}