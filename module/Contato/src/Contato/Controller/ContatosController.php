<?php
namespace Contato\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Contato\Form\ContatoForm;
use Contato\Model\Contato;

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
        //return new ViewModel(['contatos' => $this->getContatoTable()->fetchAll()]);
        
        // colocar parametros da url em um array
        $paramsUrl = [
            'pagina_atual'  => $this->params()->fromQuery('pagina', 1),
            'itens_pagina'  => $this->params()->fromQuery('itens_pagina', 10),
            'coluna_nome'   => $this->params()->fromQuery('coluna_nome', 'nome'),
            'coluna_sort'   => $this->params()->fromQuery('coluna_sort', 'ASC'),
            'search'        => $this->params()->fromQuery('search', null),
        ];

        // configuar método de paginação
        $paginacao = $this->getContatoTable()->fetchPaginator(
                /* $pagina */           $paramsUrl['pagina_atual'],
                /* $itensPagina */      $paramsUrl['itens_pagina'],
                /* $ordem */            "{$paramsUrl['coluna_nome']} {$paramsUrl['coluna_sort']}",
                /* $search */           $paramsUrl['search'],
                /* $itensPaginacao */   5
        );

        // retonar paginação mais os params de url para view
        return new ViewModel(['contatos' => $paginacao] + $paramsUrl);
    }
 
    // GET /contatos/novo
    public function novoAction()
    {
        return ['formContato' => new ContatoForm()];
    }
 
    // POST /contatos/adicionar
    public function adicionarAction()
    {
        // obtem requisição
        $request = $this->getRequest();
        
        // Verifica se a requisição é do tipo POST
        if ($request->isPost())
        {
            // instancia formulário
            $form = new ContatoForm();
            
            // instancia model contato com reagras de filtros e validações
            $modelContato = new Contato();
            
            // passa para o formulário as regras
            $form->setInputFilter($modelContato->getInputFilter());
            
            // obter e armazena valores do post
            $form->setData($request->getPost());
            
            // verifica se o formulário segue a validação proposta
            if ($form->isValid())
            {
                // popular model com valores do formulário
                $modelContato->exchangeArray($form->getData());
                
                // persistir dados do model para banco de dados
                $this->getContatoTable()->save($modelContato);
                
                // adiciona mensagem de sucesso
                $this->flashMessenger()->addSuccessMessage("Contato criado com sucesso");
                // redirecionar para action index no controller contatos
                return $this->redirect()->toRoute('contatos');
            }
            else
            {
                // renderiza para action novo com o objeto form populado,
                // com isso os erros serão tratados pelo helpers view
                return (new ViewModel())
                    ->setVariable('formContato', $form)
                    ->setTemplate('contato/contatos/novo');
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
            $this->flashMessenger()->addMessage("Contato não encontrado");

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
            //$contato = $this->getContatoTable()->find($id);
            // lógica cache objetos contatos
            $nome_cache_contato_id = "nome_cache_contato_{$id}";
            if (!$this->cache()->hasItem($nome_cache_contato_id))
            {
                $contato = $this->getContatoTable()->find($id);
                
                $this->cache()->setItem($nome_cache_contato_id, $contato);
            }
            else
            {
                $contato = $this->cache()->getItem($nome_cache_contato_id);
            }
        } catch (Exception $ex) {
            // adicionar mensagem
            $this->flashMessenger()->addErrorMessage($ex->getMessage());
            
            // redirecionar para action index
            return $this->redirect()->toRoute('contatos');
        }

        // dados eviados para detalhes.phtml
        return (new ViewModel())
                ->setTerminal($this->getRequest()->isXmlHttpRequest())
                ->setVariable('contato', $contato)
        ;
    }
 
    // GET /contatos/editar/id
    public function editarAction()
    {
        // filtra id passsado pela url
        $id = (int) $this->params()->fromRoute('id', 0);

        // se id = 0 ou não informado redirecione para contatos
        if (!$id) {
            // adicionar mensagem de erro
            $this->flashMessenger()->addMessage("Contato não encontrado");

            // redirecionar para action index
            return $this->redirect()->toRoute('contatos');
        }
        
                
        // localizar adapter do banco
        //$adapter = $this->getServiceLocator()->get('AdapterDb');

        // model ContatoTable instanciado
        //$modelContato = new ModelContato($adapter); // alias para ContatoTable
        try {
            $contato = (array) $this->getContatoTable()->find($id);
        } catch (Exception $exc) {
            // adicionar mensagem
            $this->flashMessenger()->addErrorMessage($exc->getMessage());

            // redirecionar para action index
            return $this->redirect()->toRoute('contatos');
        }
        
        // objeto form contato vazio
        $form = new ContatoForm();
        
        // popula o form contato com o objeto model contato
        $form->setData($contato);
        
        // dados eviados para editar.phtml
        return ['formContato' => $form];
    }
 
    // PUT /contatos/editar/id
    public function atualizarAction()
    {
        // obtém a requisição
        $request = $this->getRequest();

        // verifica se a requisição é do tipo post
        if ($request->isPost()) {
            
            // instancia formulario
            $form = new ContatoForm();
            
            // instancia model contato com regras de filtros e validações
            $modelContato = new Contato();
            
            // passa para o form as regras
            $form->setInputFilter($modelContato->getInputFilter());
            
            // passa os dados para o form
            $form->setData($request->getPost());

            // verifica se o formulário segue a validação proposta
            if ($form->isValid()) {

                // popula model com valores do formulário
                $modelContato->exchangeArray($form->getData());
                
                // atualiza dados do model para banco de dados
                $this->getContatoTable()->update($modelContato);
                
                // adicionar mensagem de sucesso
                $this->flashMessenger()->addSuccessMessage("Contato editado com sucesso");
                
                $nome_cache_contato_id = "nome_cache_contato_{$modelContato->id}";
                if ($this->cache()->hasItem($nome_cache_contato_id))
                {
                    $this->cache()->removeItem($nome_cache_contato_id);
                }
                
                // redirecionar para action detalhes
                return $this->redirect()->toRoute('contatos', array("action" => "detalhes", "id" => $modelContato->id,));
            } else {
                // renderiza para action editar com o objeto form populado,
                // com isso os erros serão tratados pelo helpers view
                return (new ViewModel())
                                ->setVariable('formContato', $form)
                                ->setTemplate('contato/contatos/editar');
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
            $this->flashMessenger()->addMessage("Contato não encontrado");

        } else {
            // aqui vai a lógica para deletar o contato no banco
            // 1 - solicitar serviço para pegar o model responsável pelo delete
            // 2 - deleta contato
            $this->getContatoTable()->delete($id);
            
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
    
    // GET /contatos/search?query=[nome]
    public function searchAction()
    {
        $nome = $this->params()->fromQuery('query', null);
        if (isset($nome)) {
            $result = $this->getContatoTable()->search($nome);   
        } else  {
            $result = [];  
        }

        return new \Zend\View\Model\JsonModel($result);
    }
}