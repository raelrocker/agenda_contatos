<?php
namespace Contato;
// import Contato\Model
use Contato\Model\Contato,
    Contato\Model\ContatoTable;
 
// import Zend\Db
use Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getViewHelperConfig()
    {
        return array(
            # registrar View helper com injeção de dependência
            'factories' => array(
                'menuAtivo' => function($sm) {
                    return new View\Helper\MenuAtivo($sm->getServiceLocator()->get('Request'));
                },
                'message' => function($sm) {
                    return new View\Helper\Message($sm->getServiceLocator()->get('ControllerPluginManager')->get('flashmessenger'));
                },
            )
        );
    }
    
    public function getServiceConfig()
    {
        return array (
            'factories' => array (
                'ContatoTableGateway' => function($sm) {
                    // obter adapter db atraves do service manager
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    
                    // Configurar ResultSet com nosso model Contato
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Contato());
                    
                    // retorna Tbalegateway configurado para nosso model contato
                    return new TableGateway('contatos', $adapter, null, $resultSetPrototype);
                    
                },
                'ModelContato' => function ($sm) {
                    // retorna instancia Model ContatoTable
                    return new ContatoTable($sm->get('ContatoTableGateway'));
                },
            )
        );
    }
}
