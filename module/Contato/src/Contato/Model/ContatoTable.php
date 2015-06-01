<?php
// namespace de localizacao do nosso model
namespace Contato\Model;
 
// import ZendDb
use //Zend\Db\Adapter\Adapter,
    //Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\TableGateway;
 
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
}