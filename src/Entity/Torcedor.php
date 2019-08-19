<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="torcedor")
 */
class Torcedor {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $idTorcedor;

    /**
     * @return mixed
     */
    public function getIdTorcedor()
    {
        return $this->idTorcedor;
    }

    /**
     * @param mixed $idTorcedor
     */
    public function setIdTorcedor($idTorcedor): void
    {
        $this->idTorcedor = $idTorcedor;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * @param mixed $documento
     */
    public function setDocumento($documento): void
    {
        $this->documento = $documento;
    }

    /**
     * @return mixed
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * @param mixed $cep
     */
    public function setCep($cep): void
    {
        $this->cep = $cep;
    }

    /**
     * @return mixed
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * @param mixed $endereco
     */
    public function setEndereco($endereco): void
    {
        $this->endereco = $endereco;
    }

    /**
     * @return mixed
     */
    public function getBairro()
    {
        return $this->bairro;
    }

    /**
     * @param mixed $bairro
     */
    public function setBairro($bairro): void
    {
        $this->bairro = $bairro;
    }

    /**
     * @return mixed
     */
    public function getCidade()
    {
        return $this->cidade;
    }

    /**
     * @param mixed $cidade
     */
    public function setCidade($cidade): void
    {
        $this->cidade = $cidade;
    }

    /**
     * @return mixed
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * @param mixed $uf
     */
    public function setUf($uf): void
    {
        $this->uf = $uf;
    }

    /**
     * @return mixed
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * @param mixed $telefone
     */
    public function setTelefone($telefone): void
    {
        $this->telefone = $telefone;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param mixed $ativo
     */
    public function setAtivo($ativo): void
    {
        $this->ativo = $ativo;
    }
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $nome;
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $documento;

    /**
     * @ORM\Column(type="integer")
     */
    public $cep;
    /**
     * @ORM\Column(type="text")
     */
    public $endereco;
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $bairro;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $cidade;

    /**
     * @ORM\Column(type="string", length=2)
     */
    public $uf;

    /**
     * @ORM\Column(type="float")
     */
    public $telefone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    public $mail;
    /**
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    public $ativo;

}