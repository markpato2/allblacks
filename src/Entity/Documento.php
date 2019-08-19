<?php


namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
/**
 * @ORM\Entity
 * @ORM\Table(name="documento")
 *
 *
 *
 */
class Documento
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $idDocumento;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(
     *
     *     mimeTypes = {"application/xml","application/xhtml+xml","application/vnd.ms-excel","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"},
     *     mimeTypesMessage = "Por favor subir arquivo XML ou Excel"
     * )
     */
    private $documentoFilename;

    public function getdocumentoFilename()
    {
        return $this->documentoFilename;
    }

    public function setdocumentoFilename($brochureFilename)
    {
        $this->documentoFilename = $brochureFilename;

        return $this;
    }






}