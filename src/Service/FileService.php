<?php


namespace App\Service;


use App\Entity\Torcedor;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;

class FileService
{
    private $kernel;
    /*public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }*/

    private $em;

    public function __construct(EntityManagerInterface $em,KernelInterface $kernel){
        $this->em = $em;
        $this->kernel = $kernel;
    }

    function saveToDisk(UploadedFile $image) {
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $uploadDirectory = 'uploads/documentos/'.date("Y/m/d");
        $path = $this->kernel->getProjectDir().'/public/' . $uploadDirectory;
        $extensaoDocumento= $image->guessExtension();
        $imageName = $originalFilename."_".uniqid() . '.' . $image->guessExtension();
        $image->move($path, $imageName);

       // $extensaoDocumento=$image->guessExtension();


        /*$documentoXML->move(
            $this->getParameter('documentosXML'),
            $newFilename
        );*/

        // '/'. $uploadDirectory. '/' . $imageName

        $xmlfile=  $uploadDirectory. '/' . $imageName;

        if($extensaoDocumento=="xlsx" || $extensaoDocumento=="xls"){


            // Tosses exception
            $reader = \PHPExcel_IOFactory::createReaderForFile($xmlfile);

            // Need this otherwise dates and such are returned formatted
            /** @noinspection PhpUndefinedMethodInspection */
            $reader->setReadDataOnly(true);

            // Just grab all the rows
            $wb = $reader->load($xmlfile);
            $ws = $wb->getSheet(0);
            $rows = $ws->toArray();

            foreach($rows as $row) {

                $torcedorBanco = $this->em->getRepository(Torcedor::class)->findOneBy(['documento' => $row[1]]);
                //$torcedorBanco = $entityManager->getRepository(Torcedor::class)->findOneBy(['documento' => $row[1]]);

                if ($torcedorBanco != null) {



                    ($row[0]!="NOME")?$torcedorBanco->setNome($row[0]):'';
                    ($row[2]!="CEP")?$torcedorBanco->setCep($row[2]):'';
                    ($row[3]!="ENDEREÇO")?$torcedorBanco->setEndereco($row[3]):'';
                    ($row[4]!="BAIRRO")?$torcedorBanco->setBairro($row[4]):'';
                    ($row[5]!="CIDADE")?$torcedorBanco->setCidade($row[5]):'';
                    ($row[6]!="UF")?$torcedorBanco->setUf($row[6]):'';
                    ($row[7]!="TELEFONE")?$torcedorBanco->setTelefone($row[7]):'';
                    ($row[8]!="E-MAIL")?$torcedorBanco->setMail($row[8]):'';
                    if($row[9]=="SIM"){
                        $torcedorBanco->setAtivo(true);
                    }elseif($row[9]=="NÃO"){
                        $torcedorBanco->setAtivo(false);
                    }



                    $this->em->flush();
                    $this->em->clear();

                }else {

                    // echo "entro else";
                    // die();

                    if($row[0]!="NOME") {

                        $torcedor = new Torcedor();


                        ($row[0] != "NOME") ? $torcedor->setNome($row[0]) : '';
                        ($row[1] != "DOCUMENTO") ? $torcedor->setDocumento($row[1]) : '';
                        ($row[2] != "CEP") ? $torcedor->setCep($row[2]) : '';
                        ($row[3] != "ENDEREÇO") ? $torcedor->setEndereco($row[3]) : '';
                        ($row[4] != "BAIRRO") ? $torcedor->setBairro($row[4]) : '';
                        ($row[5] != "CIDADE") ? $torcedor->setCidade($row[5]) : '';
                        ($row[6] != "UF") ? $torcedor->setUf($row[6]) : '';
                        ($row[7] != "TELEFONE") ? $torcedor->setTelefone($row[7]) : '';
                        ($row[8] != "E-MAIL") ? $torcedor->setMail($row[8]) : '';
                        if ($row[9] == "SIM") {
                            $torcedor->setAtivo(true);
                        } elseif ($row[9] == "NÃO") {
                            $torcedor->setAtivo(false);
                        }
                        $this->em->persist($torcedor);
                        $this->em->flush();
                        $this->em->clear();
                    }
                }



            }


        }elseif($extensaoDocumento=="xml") {

            //Transforma xml em Array
            $xmlstring = file_get_contents($xmlfile);


            $xml = simplexml_load_string($xmlstring);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);

            //print_r($array);
            //die();


            foreach ($array as $atributos) {
                foreach ($atributos as $descricao) {

                   //$entityManager = $entityManager = $this->em->getDoctrine()->getManager();
                    $torcedorBanco = $this->em->getRepository(Torcedor::class)->findOneBy(['documento' => $descricao['@attributes']['documento']]);


                    //$torcedorBanco = $entityManager-> getRepository(Torcedor::class)->findOneBy(['documento' => $descricao['@attributes']['documento']]);

                    if ($torcedorBanco != null) {

                        //var_dump($torcedorBanco);
                        //die();

                        $torcedorBanco->setNome($descricao['@attributes']['nome']);
                        $torcedorBanco->setDocumento($descricao['@attributes']['documento']);
                        $torcedorBanco->setCep($descricao['@attributes']['cep']);
                        $torcedorBanco->setEndereco($descricao['@attributes']['endereco']);
                        $torcedorBanco->setBairro($descricao['@attributes']['bairro']);
                        $torcedorBanco->setCidade($descricao['@attributes']['cidade']);
                        $torcedorBanco->setUf($descricao['@attributes']['uf']);
                        $torcedorBanco->setTelefone($descricao['@attributes']['telefone']);
                        $torcedorBanco->setMail($descricao['@attributes']['email']);
                        $torcedorBanco->setAtivo($descricao['@attributes']['ativo']);

                        $this->em->flush();
                        $this->em->clear();

                    } else {

                        $torcedor = new Torcedor();
                        $torcedor->setNome($descricao['@attributes']['nome']);
                        $torcedor->setDocumento($descricao['@attributes']['documento']);
                        $torcedor->setCep($descricao['@attributes']['cep']);
                        $torcedor->setEndereco($descricao['@attributes']['endereco']);
                        $torcedor->setBairro($descricao['@attributes']['bairro']);
                        $torcedor->setCidade($descricao['@attributes']['cidade']);
                        $torcedor->setUf($descricao['@attributes']['uf']);
                        $torcedor->setTelefone($descricao['@attributes']['telefone']);
                        $torcedor->setMail($descricao['@attributes']['email']);
                        $torcedor->setAtivo($descricao['@attributes']['ativo']);

                        $this->em->persist($torcedor);
                        $this->em->flush();
                        $this->em->clear();
                    }

                }


            }


        }







        return '/'. $uploadDirectory. '/' . $imageName;
        //return $imageName;
    }
}