<?php

namespace App\Controller;

use App\Entity\Documento;
use App\Entity\Torcedor;
use App\Form\DocumentoType;
use DOMDocument;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Verdant\XML2Array;
use XSLTProcessor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFormType;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;


/**
 * @Route("/documento")
 */
class DocumentoController extends EasyAdminController
{
    /**
     * @Route("/", name="documento_index", methods={"GET"})
     */
    public function index(): Response
    {
        $documentos = $this->getDoctrine()
            ->getRepository(Documento::class)
            ->findAll();

        return $this->render('documento/index.html.twig', [
            'documentos' => $documentos,
        ]);
    }





    public function new(Request $request): Response
    {
        $documento = new Documento();

        $form = $this->createForm(DocumentoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $documentoXML = $form['documento']->getData();


            if ($documentoXML) {
                $originalFilename = pathinfo($documentoXML->getClientOriginalName(), PATHINFO_FILENAME);
                //Para segurança do documento linea embaixo
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$documentoXML->guessExtension();

                $extensaoDocumento= $documentoXML->guessExtension();

                // Copia documentos para a pasta
                try {
                    $documentoXML->move(
                        $this->getParameter('documentosXML'),
                        $newFilename
                    );

                    $xmlfile= $this->getParameter('documentosXML').'/'.$newFilename;

                    //Verifica se é Excel o XLS

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

                            $entityManager = $this->getDoctrine()->getManager();
                            $torcedorBanco = $entityManager->getRepository(Torcedor::class)->findOneBy(['documento' => $row[1]]);

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



                                $entityManager->flush();
                                $entityManager->clear();

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
                                    $entityManager->persist($torcedor);
                                    $entityManager->flush();
                                    $entityManager->clear();
                                }
                            }



                        }


                    } elseif($extensaoDocumento=="xml") {

                        //Transforma xml em Array
                        $xmlstring = file_get_contents($xmlfile);


                        $xml = simplexml_load_string($xmlstring);
                        $json = json_encode($xml);
                        $array = json_decode($json, TRUE);


                        foreach ($array as $atributos) {
                            foreach ($atributos as $descricao) {

                                $entityManager = $this->getDoctrine()->getManager();


                                $torcedorBanco = $entityManager->getRepository(Torcedor::class)->findOneBy(['documento' => $descricao['@attributes']['documento']]);

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

                                    $entityManager->flush();
                                    $entityManager->clear();

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

                                    $entityManager->persist($torcedor);
                                    $entityManager->flush();
                                    $entityManager->clear();
                                }

                            }


                        }


                    }




                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $documento->setdocumentoFilename($newFilename);
            }




            //$entityManager = $this->getDoctrine()->getManager();
           // $entityManager->persist($documento);
           // $entityManager->flush();

            return $this->redirectToRoute('documento_index');
        }

        return $this->render('documento/new.html.twig', [
            //'documento' => $documento,
            'form' => $form->createView(),
        ]);
    }




    /**
     * @Route("/{idDocumento}", name="documento_show", methods={"GET"})
     */
    public function show(Documento $documento): Response
    {
        return $this->render('documento/show.html.twig', [
            'documento' => $documento,
        ]);
    }

    /**
     * @Route("/{idDocumento}/edit", name="documento_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Documento $documento): Response
    {
        $form = $this->createForm(DocumentoType::class, $documento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('documento_index');
        }

        return $this->render('documento/edit.html.twig', [
            'documento' => $documento,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idDocumento}", name="documento_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Documento $documento): Response
    {
        if ($this->isCsrfTokenValid('delete'.$documento->getIdDocumento(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($documento);
            $entityManager->flush();
        }

        return $this->redirectToRoute('documento_index');
    }






}
