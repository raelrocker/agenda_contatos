<?php
// namespace de localizacao do nosso model
namespace Contato\Model;
 
// import ZendDb
use //Zend\Db\Adapter\Adapter,
    //Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\TableGateway;

// import for fetchPaginator
use Zend\Db\Sql\Select,
    Zend\Db\ResultSet\HydratingResultSet,
    Zend\Stdlib\Hydrator\Reflection,
    Zend\Paginator\Adapter\DbSelect,
    Zend\Paginator\Paginator;
 
class ContatoTable
{
    protected $tableGateway;
 
    /**
     * Contrutor com dependencia do Adapter do Banco
     * 
     * @param Adapter $adapter
     */
    public function __construct(TableGateway $tableGateway)
    {
        //$resultSetPrototype = new ResultSet();
        //$resultSetPrototype->setArrayObjectPrototype(new Contato());
 
        //$this->tableGateway = new TableGateway('contatos', $adapter, null, $resultSetPrototype);
        $this->tableGateway = $tableGateway;
    }
 
    /**
     * Recuperar todos os elementos da tabela contatos
     * 
     * @return ResultSet
     */
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }
 
    /**
     * Localizar linha especifico pelo id da tabela contatos
     * 
     * @param type $id
     * @return ModelContato
     * @throws Exception
     */
    public function find($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new Exception("Não foi encontrado contado de id = {$id}");
        }
 
        return $row;
    }
    
    public function save(Contato $contato)
    {
        $timeNow = new \DateTime();
        
        $data = [
            'nome'                  => $contato->nome,
            'telefone_principal'    => $contato->telefone_principal,
            'telefone_secundario'   => $contato->telefone_secundario,
            'data_criacao'          => $timeNow->format('Y-m-d H:i:s'),
            'data_atualizacao'      => $timeNow->format('Y-m-d H:i:s'),
        ];
        
        return $this->tableGateway->insert($data);
    }
    
    public function update(Contato $contato)
    {
        $timeNow = new \DateTime();
        
        $data = [
            'nome'                  => $contato->nome,
            'telefone_principal'    => $contato->telefone_principal,
            'telefone_secundario'   => $contato->telefone_secundario,
            'data_atualizacao'      => $timeNow->format('Y-m-d H:i:s'),
        ];
        
        $id = (int) $contato->id;
        if ($this->find($id))
        {
            $this->tableGateway->update($data, array('id' => $id));
        }
        else
        {
            throw new Exception("Contato #{$id} inexistente");
        }
    }
    
    public function delete($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
    
    public function fetchPaginator($pagina = 1, $itensPagina = 10, $ordem = 'nome ASC', $like = null, $itensPaginacao = 5)
    {
        // preparar um select para tabela contato com uma ordem
        $select = (new Select('contatos'))->order($ordem);

        if (isset($like)) {
            $select
                    ->where
                    ->like('id', "%{$like}%")
                    ->or
                    ->like('nome', "%{$like}%")
                    ->or
                    ->like('telefone_principal', "%{$like}%")
                    ->or
                    ->like('data_criacao', "%{$like}%")
            ;
        }

        // criar um objeto com a estrutura desejada para armazenar valores
        $resultSet = new HydratingResultSet(new Reflection(), new Contato());

        // criar um objeto adapter paginator
        $paginatorAdapter = new DbSelect(
            // nosso objeto select
            $select,
            // nosso adapter da tabela
            $this->tableGateway->getAdapter(),
            // nosso objeto base para ser populado
            $resultSet
        );
    
        // resultado da paginação
        return (new Paginator($paginatorAdapter))
                // pagina a ser buscada
                ->setCurrentPageNumber((int) $pagina)
                // quantidade de itens na página
                ->setItemCountPerPage((int) $itensPagina)
                ->setPageRange((int) $itensPaginacao); 
    }
    
    /**
    * Localizar contatos pelo nome
    * 
    * @param type $nome
    * @return type Array
    */
    public function search($nome)
    {
        // preparar objeto SQL
        $adapter = $this->tableGateway->getAdapter();
        $sql     = new \Zend\Db\Sql\Sql($adapter);

        // montagem do select com where, like e limit para tabela contatos
        $select = (new Select('contatos'))->limit(8);
        $select
                ->columns(array('id', 'nome'))
                ->where
                ->like('nome', "%{$nome}%")
        ;

        // executar select
        $statement = $sql->getSqlStringForSqlObject($select);
        $results   = $adapter->query($statement, $adapter::QUERY_MODE_EXECUTE);

        return $results;
    }
}