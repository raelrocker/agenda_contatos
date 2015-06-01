<?php
namespace Contato\Model;

use \Zend\InputFilter\InputFilterAwareInterface;
use \Zend\InputFilter\InputFilter;
use \Zend\InputFilter\InputFilterInterface;


class Contato implements InputFilterAwareInterface
{
    public $id;
    public $nome;
    public $telefone_principal;
    public $telefone_secundario;
    public $data_criacao;
    public $data_atualizacao;
    protected $inputFilter;
    
    public function exchangeArray($data)
    {
        $this->id                   = (!empty($data['id'])) ? $data['id'] : null;
        $this->nome                 = (!empty($data['nome'])) ? $data['nome'] : null;
        $this->telefone_principal   = (!empty($data['telefone_principal'])) ? $data['telefone_principal'] : null;
        $this->telefone_secundario  = (!empty($data['telefone_secundario'])) ? $data['telefone_secundario'] : null;
        $this->data_criacao         = (!empty($data['data_criacao'])) ? $data['data_criacao'] : null;
        $this->data_atualizacao     = (!empty($data['data_atualizacao'])) ? $data['data_atualizacao'] : null;
    }

    /**
     * Aqui estão todas as regras de validações e filtros para o formulário.
     * 
     * @return InputFilter
     */
    public function getInputFilter() {
        if (!$this->inputFilter)
        {
            $inputFilter = new InputFilter();
            
            // filtro para o campo de id
            $inputFilter->add([
                'name'      => 'id',
                'required'  => true,
                'filters'   => [
                    ['name' => 'Int'], # Transforma string para int
                ],
            ]);
            
            // filtro para o campo de nome
            $inputFilter->add([
                'name'      => 'nome',
                'required'  => true,
                'filters'   => [
                    ['name' => 'StripTags'], # remove xml e html da string
                    ['name' => 'StringTrim'], #remove espaços
                    ['name' => 'StringToUpper'], #tranforma string para maíusculo
                ],
                'validators' => [
                    [
                        'name'      => 'NotEmpty',
                        'options'   => [
                            'messages' => [
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Campo obrigatório'
                            ],
                        ], 
                    ],
                    [
                        'name'      => 'StringLength',
                        'options'   => [
                            'encoding'  => 'UTF-8',
                            'min'       => 3,
                            'max'       => 100,
                            'messages'  => [
                                \Zend\Validator\StringLength::TOO_SHORT => 'Mínimo de caracteres aceitáveis %min%',
                                \Zend\Validator\StringLength::TOO_LONG => 'Máximo de caracteres aceitáveis %max%',
                            ],
                        ],
                    ],
                ],
            ]);
            
            // filtro para o campo de telefone principal
            $inputFilter->add([
                'name'      => 'telefone_principal',
                'required'  => true,
                'filters'   => [
                    ['name' => 'StripTags'], # remove xml e html da string
                    ['name' => 'StringTrim'], #remove espaços
                ],
                'validators' => [
                    [
                        'name'      => 'NotEmpty',
                        'options'   => [
                            'messages' => [
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Campo obrigatório'
                            ],
                        ], 
                    ],
                    [
                        'name'      => 'StringLength',
                        'options'   => [
                            'encoding'  => 'UTF-8',
                            'min'       => 5, #xxxxxxxx
                            'max'       => 15, #(xxx)xxxx-xxxxx
                            'messages'  => [
                                \Zend\Validator\StringLength::TOO_SHORT => 'Mínimo de caracteres aceitáveis %min%',
                                \Zend\Validator\StringLength::TOO_LONG => 'Máximo de caracteres aceitáveis %max%',
                            ],
                        ],
                    ],
                ],
            ]);
            
            // filtro para o campo de telefone secundário
            $inputFilter->add([
                'name'      => 'telefone_secundario',
                'required'  => false,
                'filters'   => [
                    ['name' => 'StripTags'], # remove xml e html da string
                    ['name' => 'StringTrim'], #remove espaços
                ],
                'validators' => [
                    [
                        'name'      => 'StringLength',
                        'options'   => [
                            'encoding'  => 'UTF-8',
                            'min'       => 3,
                            'max'       => 100,
                            'messages'  => [
                                \Zend\Validator\StringLength::TOO_SHORT => 'Mínimo de caracteres aceitáveis %min%',
                                \Zend\Validator\StringLength::TOO_LONG => 'Máximo de caracteres aceitáveis %max%',
                            ],
                        ],
                    ],
                ],
            ]);
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new Exception('Não utilizado');
    }

}