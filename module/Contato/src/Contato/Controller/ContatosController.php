<?php
namespace Contato\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContatosController extends AbstractActionController
{
    protected $contatoTable;
    // GET /contatos
    public function indexAction()
    {
        // localizar adapter do banco
        //$adapter = $this->getServiceLocator()->get('AdapterDb');

        // model ContatoTable instanciado
        //$modelContato = new ModelContato($adapter); // alias para ContatoTable

        // enviar para view o array com key contatos e value com todos os contatos
        //return new ViewModel(array('contatos' => $modelContato->fetchAll()));
        
        // enviar para view o array com key contatos e value com todos os contatos
        return new ViewModel(['contatos' => $this->getContatoTable()->fetchAll()]);
    }
 
    // GET /contatos/novo
    public function novoAction()
    {
    }
 
    // POST /contatos/adicionar
    public function adicionarAction()
    {
        // obtem requisição
        $request = $this->getRequest();
        
        // Verifica se a requisição é do tipo POST
        if ($request->isPost())
        {
            // obter e armazena valores do post
            $postData = $request->getPost()->toArray();
            $formularioValido = TRUE;
            
            // verifica se o formulário segue a validação proposta
            if ($formularioValido)
            {
                // adiciona mensagem de sucesso
                $this->flashMessenger()->addSuccessMessage("Contato criado");
                // redirecionar para action index no controller contatos
                return $this->redirect()->toRoute('contatos');
            }
            else
            {
                // adicioanar mensagem de erro
                $this->flashMessenger()->addErrorMessage("Erro ao criar contato");
                // redirecionar para action novo no controller contatos
                $this->redirect()->toRoute('contatos', array('action' => 'novo'));
            }
        }
    }
 
    // GET /contatos/detalhes/id
    public function detalhesAction()
    {
        // filtra id passsado pela url
        $id = (int) $this->params()->fromRoute('id', 0);

        // se id = 0 ou não informado redirecione para contatos
        if (!$id) {
            // adicionar mensagem
            $this->flashMessenger()->addMessage("Contato não encotrado");

            // redirecionar para action index
            return $this->redirect()->toRoute('contatos');
        }

        // aqui vai a lógica para pegar os dados referente ao contato
        // 1 - solicitar serviço para pegar o model responsável pelo find
        // 2 - solicitar form com dados desse contato encontrado
        // formulário com dados preenchidos
        
        // localizar adapter do banco
        //$adapter = $this->getServiceLocator()->get('AdapterDb');
        
        // model ContatoTable instanciado
        //$modelContato = new ModelContato($adapter);
        
        try
        {
            $form = (array) $this->getContatoTable()->find($id);
        } catch (Exception $ex) {
            // adicionar mensagem
            $this->flashMessenger()->addErrorMessage($ex->getMessage());
            
            // redirecionar para action index
            return $this->redirect()->toRoute('contatos');
        }

        // dados eviados para detalhes.phtml
        return ['id' => $id, 'form' => $form];
    }
 
    // GET /contatos/editar/id
    public function editarAction()
    {
        // filtra id passsado pela url
        $id = (int) $this->params()->fromRoute('id', 0);

        // se id = 0 ou não informado redirecione para contatos
        if (!$id) {
            // adicionar mensagem de erro
            $this->flashMessenger()->addMessage("Contato não encotrado");

            // redirecionar para action index
            return $this->redirect()->toRoute('contatos');
        }

        // aqui vai a lógica para pegar os dados referente ao contato
        // 1 - solicitar serviço para pegar o model responsável pelo find
        // 2 - solicitar form com dados desse contato encontrado
        // formulário com dados preenchidos
    //        $form = array(
    //            'nome'                  => 'Igor Rocha',
    //            "telefone_principal"    => "(085) 8585-8585",
    //            "telefone_secundario"   => "(085) 8585-8585",
    //        );
        // localizar adapter do banco
        //$adapter = $this->getServiceLocator()->get('AdapterDb');

        // model ContatoTable instanciado
        //$modelContato = new ModelContato($adapter); // alias para ContatoTable
        try {
            $form = (array) $this->getContatoTable()->find($id);
        } catch (Exception $exc) {
            // adicionar mensagem
            $this->flashMessenger()->addErrorMessage($exc->getMessage());

            // redirecionar para action index
            return $this->redirect()->toRoute('contatos');
        }

        // dados eviados para editar.phtml
        return ['id' => $id, 'form' => $form];
    }
 
    // PUT /contatos/editar/id
    public function atualizarAction()
    {
        // obtém a requisição
        $request = $this->getRequest();

        // verifica se a requisição é do tipo post
        if ($request->isPost()) {
            // obter e armazenar valores do post
            $postData = $request->getPost()->toArray();
            $formularioValido = true;

            // verifica se o formulário segue a validação proposta
            if ($formularioValido) {
                // aqui vai a lógica para editar os dados à tabela no banco
                // 1 - solicitar serviço para pegar o model responsável pela atualização
                // 2 - editar dados no banco pelo model

                // adicionar mensagem de sucesso
                $this->flashMessenger()->addSuccessMessage("Contato editado com sucesso");

                // redirecionar para action detalhes
                return $this->redirect()->toRoute('contatos', array("action" => "detalhes", "id" => $postData['id'],));
            } else {
                // adicionar mensagem de erro
                $this->flashMessenger()->addErrorMessage("Erro ao editar contato");

                // redirecionar para action editar
                return $this->redirect()->toRoute('contatos', array('action' => 'editar', "id" => $postData['id'],));
            }
        }
    }
 
    // DELETE /contatos/deletar/id
    public function deletarAction()
    {
        // filtra id passsado pela url
        $id = (int) $this->params()->fromRoute('id', 0);

        // se id = 0 ou não informado redirecione para contatos
        if (!$id) {
            // adicionar mensagem de erro
            $this->flashMessenger()->addMessage("Contato não encotrado");

        } else {
            // aqui vai a lógica para deletar o contato no banco
            // 1 - solicitar serviço para pegar o model responsável pelo delete
            // 2 - deleta contato

            // adicionar mensagem de sucesso
            $this->flashMessenger()->addSuccessMessage("Contato de ID $id deletado com sucesso");

        }

        // redirecionar para action index
        return $this->redirect()->toRoute('contatos');
    }
    
    /**
    * Metodo privado para obter instacia do Model ContatoTable
    * 
    * @return ContatoTable
    */
    private function getContatoTable()
    {
        // localizar adpater do banco
        //$tableGateway = $this->getServiceLocator()->get('ContatoTableGateway');
        // retorna model ContatoTable
        //return new ModelContato($tableGateway);
        if (!$this->contatoTable)
        {
            $this->contatoTable = $this->getServiceLocator()->get('ModelContato');
        }
        return $this->contatoTable;
    }
}