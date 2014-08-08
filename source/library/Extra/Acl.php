<?php
class Extra_Acl
        extends Zend_Application_Resource_ResourceAbstract
{
    const DEFAULT_REGISTRY_KEY='Acl';
    protected $_cache;
    protected $_db;

    function init()
    {
        
        $result = $this->getSession();
        if ($result === false) {
            $this->_cache = $this->getCache($this->_options['cache']);
            $acl = new Zend_Acl();
            foreach ($this->getTable('role') as $row):

                $acl->addRole(new Zend_Acl_Role($row['id'])/*, $row['IdParent']*/);

            endforeach;

            foreach ($this->getTable('resource') as $row):
                $acl->addResource(new Zend_Acl_Resource(trim($row['URL_MENU'])));
            endforeach;
            foreach ($this->getTable('privilege') as $row):
                $acl->$row['PERMISO']($row['ID_PERFIL'], trim($row['URL_MENU'])); //Insertamos la Url
            endforeach;
            //exit;
            Zend_Registry::set(self::DEFAULT_REGISTRY_KEY, $acl);
            return $acl;
        } else
            return $result;
    }

    function getSession()
    {
        if (Zend_Session::isStarted()) {
            $session = new Zend_Session_Namespace('Acl');
            if (isset($session->acl)) {
                return $this->acl;
            }
        }
        return false;
    }

    function getTable($name)
    {
        if (!$result = $this->_cache->load($this->_options[$name]['table'])) {
            $table = new Zend_Db_Table($this->_options[$name]['table']);
            if ($name == 'privilege') {
                $sql = new Zend_Db_Select($table->getDefaultAdapter());
                $sql->from(
                    array('acl' => $this->_options['privilege']['table']),
                    array('acl.ID_PERFIL')
                )
                ->join(
                    array('r' => $this->_options['resource']['table']),
                    'acl.ID_MENU=r.ID_MENU', array('r.URL_MENU','acl.PERMISO')
                );
                $result = $table->getAdapter()->fetchAll($sql);
            } else {
                $result = $table->fetchAll();
            }
            $this->_cache->save($result, $this->_options[$name]['table']);
        }
        return $result;
    }

    function getCache($name)
    {
        try {
            $bootstrap = $this->getBootstrap();
            $cachemanager = $bootstrap->getPluginResource('cachemanager')->getCacheManager();
            return $cachemanager->getCache($name);
        } catch (Zend_Application_Resource_Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

}
