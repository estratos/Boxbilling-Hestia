<?php
/**
 * BoxBilling
 *
 * @copyright BoxBilling, Inc (http://www.boxbilling.com)
 * @license   Apache-2.0
 *
 * Copyright BoxBilling, Inc
 * This source file is subject to the Apache-2.0 License that is bundled
 * with this source code in the file LICENSE
 */

class Server_Manager_Hestia extends Server_Manager
{
    /**
     * Method is called just after obejct contruct is complete.
     * Add required parameters checks here. 
     */
	public function init()
    {
        
	}

    /**
     * Return server manager parameters.
     * @return type 
     */
    public static function getForm()
    {
        return array(
            'label'     =>  'Hestia Server Manager',
        );
    }

    /**
     * Returns link to account management page
     * 
     * @return string 
     */
    public function getLoginUrl()
    {

     
        $host = 'http';
        if ($this->_config['secure']) {
        $host .= 's';
        }
            $host .= '://' . $this->_config['host'] . ':'.$this->_config['port'];


        return $host;
    }
   






    /**
     * Check if Package exists
     * @param Server_Package $package
     * @return bool
     */
    private function _checkPackageExists(Server_Package $package, $create = false)
    {
        $name = $this->_getPackageName($package);
        // Server cli commands
        $hst_command = 'v-list-user-packages';
        $hst_returncode = 'yes';
        $hst_format = 'json';
        ///// create params
        $postvars = array(
    
            'returncode' => $hst_returncode,
            'cmd' => $hst_command,
            'user' => $this->_config['username'],
            'password' => $this->_config['password'],
            'arg1' => $hst_format
            						
        
        ); 

        $json = $this->_makerequest($postvars);
        
        $data = json_decode($json, true);
        $packagekeys = array_keys($data);

        $exists = false;

        //// find package name in array
        foreach ($packagekeys as $p) {
            
            if ( $p == $name) {
                $exists = true;
                break;
            }
        }

        if(!$create) {
            return $exists;
        }

        if (!$exists) {
        	//$var_hash['name']           = $name;
			//$var_hash['webtemplate']			= $package->getQuota();
            //$var_hash['backendtemplate']		= $package->getBandwidth();
			//$var_hash['proxytemplate']			= $package->getMaxSubdomains();
			//$var_hash['dnstemplate']		= $package->getMaxParkedDomains();
			//$var_hash['webdomains']		= $package->getMaxDomains();
			//$var_hash['webaliases']			= $package->getMaxFtp();
			//$var_hash['dnsdomains']			= $package->getMaxSql();
			//$var_hash['dnsrecords']			= $package->getMaxPop();
            
			//$var_hash['maildomains']			= $package->getCustomValue('cgi');
			//$var_hash['mailaccounts']		    = $package->getCustomValue('frontpage');
            //$var_hash['diskquota']			= $package->getCustomValue('cpmod');
			//$var_hash['bandwidth']			= $package->getCustomValue('maxlst');
            //$var_hash['backup']				= $package->getCustomValue('maxftp');
            //$var_hash['time']		            = $package->getCustomValue('maxsql');
            //$var_hash['date']				    = $package->getCustomValue('maxpop');
            //$var_hash['shell']		        = $package->getCustomValue('hasshell');

            $postvars = array(
    
                'returncode' => $hst_returncode,
                'cmd' => $hst_command,
                'user' => $this->_config['username'],
                'password' => $this->_config['password'],
                'arg1' => 'default.pkg',
                'arg2' => $name                       
            
            ); 

            $json = $this->_makerequest($postvars);   // add package to hestia server
            $data = json_decode($json, true);         // check response
            
        }

        return $exists;
    }

    private function _getPackageNameaddPrefix(Server_Package $package)
    {
        $name = $package->getName();
        if($this->_config['username'] != 'root') {
            $name = $this->_config['username'].'_'.$name;
        }
        
        return $name;
    }

private function _getPackageName(Server_Package $package)
    {
        $name = $package->getName();
        
        return $name;
    }
private function _makeRequest($params)
    {

$host = 'http';
		if ($this->_config['secure']) {
			$host .= 's';
		}
		$host .= '://' . $this->_config['host'] . ':'.$this->_config['port'].'/api/';

    	
    	
// Server credentials


$params['user'] = $this->_config['username'];
$params['password'] = $this->_config['password'];
   	
    	
// Send POST query via cURL
$postdata = http_build_query($params);
$curl = curl_init();
$timeout = 5;

curl_setopt($curl, CURLOPT_URL, $host);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);

$result = curl_exec($curl);

curl_close($curl);

		if(strpos($result, 'Error')!== false){
throw new Server_Exception('Connection to server failed  '.$result);
    	}

		
			return $result;
    }


    /**
     * Returns link to reseller account management
     * @return string 
     */
    public function getResellerLoginUrl()
    {
        return 'http://www.google.com?q=whm';
    }

    /**
     * This method is called to check if configuration is correct
     * and class can connect to server
     * 
     * @return boolean 
     */
    public function testConnection()
    {
      

    
           
        // Server credentials
        $vst_command = 'v-check-user-password';
        $vst_returncode = 'yes';


        // Prepare POST query
        $postvars = array(
    
        'returncode' => $vst_returncode,
        'cmd' => $vst_command,
        'arg1' => $this->_config['username'],
        'arg2' => $this->_config['password'],

        );

    
// Make request and check sys info
$result = $this->_makeRequest($postvars);

if(strpos($result, 'Error')!== false){
throw new Server_Exception('Connection to server failed  '.$result);
    	}
else {

if ($result == 0) {
    		return true;
    	} else {
    		throw new Server_Exception('Connection to server failed '.$result);
    	}

}
		return true;


    }


    /**
     * MEthods retrieves information from server, assignes new values to
     * cloned Server_Account object and returns it.
     * @param Server_Account $a
     * @return Server_Account 
     */
    public function synchronizeAccount(Server_Account $a)
    {
        $this->getLog()->info('Synchronizing account with server '.$a->getUsername());
        $new = clone $a;
        //@example - retrieve username from server and set it to cloned object
        //$new->setUsername('newusername');
        return $new;
    }

   
  

	
	
    /**
     * Create new account on server
     * 
     * @param Server_Account $a 
     */
	public function createAccount(Server_Account $a)
    {

                  $p = $a->getPackage();
           $packname = $this->_getPackageName($p);
		//// check if package exists
        
        //if (!$this->_checkPackageExists($packname, false)) {
            //$this->_createPackage($packname);
       // }
		
		$client = $a->getClient();
        // Server credentials
        $vst_command = 'v-add-user';
        $vst_returncode = 'yes';
        
        $fullname = $client->getFullName();


    // Prepare POST query
    $postvars = array(
    
    'returncode' => $vst_returncode,
    'cmd' => $vst_command,
    'user' => $this->_config['username'],
    'password' => $this->_config['password'],
    'arg1' => $a->getUsername(),
    'arg2' => $a->getPassword(),
    'arg3' => $client->getEmail(),
    'arg4' => $packname,
    'arg5' => $fullname
   						

    );    
        // Make request and create user 
            $result = $this->_makeRequest($postvars);
            if($result == 0)   /// no errors   4 user is already taken   3 package name does not exist 
                    {
 
                   // throw new Server_Exception('Server Manager Hestia CP : create result '.$result.$a->getUsername().' '.$a->getDomain() );

            if (  $this->_createUserDomain($a->getUsername(), $a->getDomain() , $a->getIP() )  ) { 

              //  throw new Server_Exception('Server Manager Hestia CP : create domain true with ');

                                                }
            else {
                
                throw new Server_Exception('Server Manager Hestia CP : create domain error with '.$a->getUsername().' '.$a->getDomain());
            
            }
    

            return true;
            }

            else if($result != 0) {
                    throw new Server_Exception('Server Manager Hestia CP Error: User name exists on server, please choose another one '.$result.' '.$a->getUsername().' '.$a->getPassword().' '.$client->getEmail().' '.$packname.' '.$fullname);
            return false;
            }
        if($a->getReseller()) {
            $this->getLog()->info('Creating reseller hosting account');
        } else {
            $this->getLog()->info('Creating shared hosting account');
        }
    }        

    /**
     * Suspend account on server
     * @param Server_Account $a 
     */
	public function suspendAccount(Server_Account $a)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Suspending reseller hosting account');
        } else {
            $this->getLog()->info('Suspending shared hosting account');
        }
      // Server cli commands
      $hst_command = 'v-suspend-user';
      $hst_returncode = 'yes';
      $hst_format = 'json';
      ///// create params
      $postvars = array(
  
          'returncode' => $hst_returncode,
          'cmd' => $hst_command,
          'user' => $this->_config['username'],
          'password' => $this->_config['password'],
          'arg1' => $a->getUsername(),
          'arg2' => 'yes'
                                  
      
      ); 

      $json = $this->_makerequest($postvars);
            

      if($json != '0'){
        throw new Server_Exception('Server Manager Hestia CP Error: suspend account failure '.$json);
        return false;
        }

	}


/**
     * Check if Package exists
     * @param Server_Package $package
     * @return bool
     */
    public function _createUserDomain($username, $domain , $serverip) {
        $this->getLog()->info('Creating user domain');
             

        // Server cli commands
        $hst_command = 'v-add-domain';
        $hst_returncode = 'yes';
        $hst_format = 'json';
        ///// create params
        $postvars = array(
            'returncode' => $hst_returncode,
            'cmd' => $hst_command,
            'user' => $this->_config['username'],
            'password' => $this->_config['password'],
            'arg1' => $username,
            'arg2' => $domain,
            'arg3' => $serverip
        );

        
        $json = $this->_makerequest($postvars);
        //throw new Server_Exception('Server Manager Vesta CP Error: Create Domain result '.$json);
        //$data = json_decode($json, true);
        //$packagekeys = array_keys($data);
        if($json != '0'){
            throw new Server_Exception('Server Manager Hestia CP Error: Create Domain failure '.$json);
            return false;
            }

        return true;
    }



    /**
     * Unsuspend account on server
     * @param Server_Account $a 
     */
	public function unsuspendAccount(Server_Account $a)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Unsuspending reseller hosting account');
        } else {
            $this->getLog()->info('Unsuspending shared hosting account');
        }
        // Server cli commands
      $hst_command = 'v-unsuspend-user';
      $hst_returncode = 'yes';
      $hst_format = 'json';
      ///// create params
      $postvars = array(
  
          'returncode' => $hst_returncode,
          'cmd' => $hst_command,
          'user' => $this->_config['username'],
          'password' => $this->_config['password'],
          'arg1' => $a->getUsername(),
          'arg2' => 'yes'
                                  
      
      ); 

      $json = $this->_makerequest($postvars);

      if($json != '0'){
        throw new Server_Exception('Server Manager Hestia CP Error: unsuspend account failure '.$json);
        return false;
        }

      return true;
	}

    /**
     * Cancel account on server
     * @param Server_Account $a 
     */
	public function cancelAccount(Server_Account $a)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Canceling reseller hosting account');
        } else {
            $this->getLog()->info('Canceling shared hosting account');
        }
    // Server cli commands
    $hst_command = 'v-suspend-user';    /// at this time for security reasons delete accounts from server are disabled
    $hst_returncode = 'yes';
    $hst_format = 'json';
    ///// create params
    $postvars = array(

        'returncode' => $hst_returncode,
        'cmd' => $hst_command,
        'user' => $this->_config['username'],
        'password' => $this->_config['password'],
        'arg1' => $a->getUsername(),
        'arg2' => 'yes'
                                
    
    ); 

    $json = $this->_makerequest($postvars);

        if($json != '0'){
            throw new Server_Exception('Server Manager Hestia CP Error: delete account failure '.$json);
            return false;
            }
            return true;
	}

    /**
     * Change account package on server
     * @param Server_Account $a
     * @param Server_Package $p 
     */
	public function changeAccountPackage(Server_Account $a, Server_Package $p)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Updating reseller hosting account');
        } else {
            $this->getLog()->info('Updating shared hosting account');
        }
        
        $p->getName();
        $p->getQuota();
        $p->getBandwidth();
        $p->getMaxSubdomains();
        $p->getMaxParkedDomains();
        $p->getMaxDomains();
        $p->getMaxFtp();
        $p->getMaxSql();
        $p->getMaxPop();
        
        $p->getCustomValue('param_name');


	}

    /**
     * Change account username on server
     * @param Server_Account $a
     * @param type $new - new account username
     */
    public function changeAccountUsername(Server_Account $a, $new)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Changing reseller hosting account username');
        } else {
            $this->getLog()->info('Changing shared hosting account username');
        }
    }

    /**
     * Change account domain on server
     * @param Server_Account $a
     * @param type $new - new domain name
     */
    public function changeAccountDomain(Server_Account $a, $new)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Changing reseller hosting account domain');
        } else {
            $this->getLog()->info('Changing shared hosting account domain');
        }
       //// changes account domain name safely no files are lossed
       
       
       /// change domain name v-change-web-domain-name  USER DOMAIN NEW_DOMAIN [RESTART]

         $hst_command = 'v-change-web-domain-name';
            $hst_returncode = 'yes';
            $hst_format = 'json';
            ///// create params
            $postvars = array(
                'returncode' => $hst_returncode,
                'cmd' => $hst_command,
                'user' => $this->_config['username'],
                'password' => $this->_config['password'],
                'arg1' => $a->getUsername(),
                'arg2' => $a->getDomain(),
                'arg3' => $new,
                'arg4' => 'yes'
            );
            
            $json = $this->_makerequest($postvars);
            if($json != '0'){
                throw new Server_Exception('Server Manager Hestia CP Error: change account domain failure '.$json);
                return false;
                }
            return true;
            
    }

    /**
     * Change account password on server
     * @param Server_Account $a
     * @param type $new - new password
     */
    public function changeAccountPassword(Server_Account $a, $new)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Changing reseller hosting account password');
        } else {
            $this->getLog()->info('Changing shared hosting account password');
        }

        $hst_command = 'v-change-user-password';
        $hst_returncode = 'yes';
        $hst_format = 'json';
        ///// create params

        $postvars = array(
            'returncode' => $hst_returncode,
            'cmd' => $hst_command,
            'user' => $this->_config['username'],
            'password' => $this->_config['password'],
            'arg1' => $a->getUsername(),
            'arg2' => $new
           
        );

        $json = $this->_makerequest($postvars);

        if($json != '0'){
            throw new Server_Exception('Server Manager Hestia CP Error: change account password failure '.$json);
            return false;
            }
        return true;


    }

    /**
     * Change account IP on server
     * @param Server_Account $a
     * @param type $new - account IP
     */
    public function changeAccountIp(Server_Account $a, $new)
    {
        if($a->getReseller()) {
            $this->getLog()->info('Changing reseller hosting account ip');
        } else {
            $this->getLog()->info('Changing shared hosting account ip');
        }
        
        $hst_command = 'v-change-web-domain-ip';
        $hst_returncode = 'yes';
        $hst_format = 'json';
        ///// create params

        $postvars = array(
            'returncode' => $hst_returncode,
            'cmd' => $hst_command,
            'user' => $this->_config['username'],
            'password' => $this->_config['password'],
            'arg1' => $a->getUsername(),
            'arg2' => $a->getDomain(),
            'arg3' => $new,
            'arg4' => 'yes'
        );

        $json = $this->_makerequest($postvars);

        if($json != '0'){
            throw new Server_Exception('Server Manager Hestia CP Error: change account ip failure '.$json);
            return false;
            }
        return true;
        
    }
}
