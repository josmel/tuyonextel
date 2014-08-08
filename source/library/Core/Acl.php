<?php

class Core_Acl extends Zend_Acl
{

    const GUEST = 'guest';

    public function __construct()
    {
        $this->addRole(new Zend_Acl_Role(self::GUEST));

        $this->add(new Zend_Acl_Resource('admin::error::error'));
        $this->add(new Zend_Acl_Resource('admin::index::error404'));
        $this->add(new Zend_Acl_Resource('admin::index::index'));
        $this->add(new Zend_Acl_Resource('admin::index::login'));
        $this->add(new Zend_Acl_Resource('admin::index::logout'));
        
        $this->add(new Zend_Acl_Resource('landing::*'));
        $this->add(new Zend_Acl_Resource('office::*'));
        $this->add(new Zend_Acl_Resource('service::*'));
        $this->add(new Zend_Acl_Resource('challenge::*'));
        $this->add(new Zend_Acl_Resource('admin-challenge::*'));

        $this->allow(self::GUEST, 'admin::error::error');
        $this->allow(self::GUEST, 'admin::index::error404');
        $this->allow(self::GUEST, 'admin::index::index');
        $this->allow(self::GUEST, 'admin::index::login');
        $this->allow(self::GUEST, 'admin::index::logout');
        
        $this->allow(self::GUEST, 'landing::*');
        $this->allow(self::GUEST, 'office::*');
        $this->allow(self::GUEST, 'service::*');
        $this->allow(self::GUEST, 'challenge::*');
        $this->allow(self::GUEST, 'admin-challenge::*');
        
        $modelAcl = new Application_Model_Acl();
        $listAcl = $modelAcl->getListResources();
        
        foreach($listAcl as $resource) {
            try {
                if(!$this->has($resource))
                    $this->add(new Zend_Acl_Resource($resource));
            } catch (Exception $ex) {
                
            }
        }
        $modelRole = new Application_Model_Role();
        $roles = $modelRole->getAllRoles();
        
        foreach($roles as $item) {
            try {
                $this->addRole(new Zend_Acl_Role($item['desrol']), self::GUEST);
                $aclsRole = $modelAcl->getAclByRole($item['idrol']);
                foreach($aclsRole as $permission) $this->allow($item['desrol'], $permission);
            } catch (Exception $ex) {
                
            }
        }
        //  $this->add(new Zend_Acl_Resource('admin::tipo-antecedentes'));
        //PERMISOS
       
    }

}
