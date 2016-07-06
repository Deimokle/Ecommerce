<?php

namespace EcommerceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use EcommerceBundle\Entity\Produit;
use EcommerceBundle\Form\ProduitType;

/**
 * Produit controller.
 *
 */
class ProduitController extends Controller
{
    /**
     * Lists all Produit entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('EcommerceBundle:Produit')->findAll();

        return $this->render('EcommerceBundle:produit:index.html.twig', array(
            'produits' => $produits,
        ));
    }

    /**
     * Creates a new Produit entity.
     *
     */
    public function newAction(Request $request)
    {
        $produit = new Produit();
        $form = $this->createForm('EcommerceBundle\Form\ProduitType', $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('produit_show', array('id' => $produit->getId()));
        }

        return $this->render('EcommerceBundle:produit:new.html.twig', array(
            'produit' => $produit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Produit entity.
     *
     */
    public function showAction(Produit $produit)
    {
        $deleteForm = $this->createDeleteForm($produit);

        return $this->render('EcommerceBundle:produit:show.html.twig', array(
            'produit' => $produit,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Produit entity.
     *
     */
    public function editAction(Request $request, Produit $produit)
    {
        $deleteForm = $this->createDeleteForm($produit);
        $editForm = $this->createForm('EcommerceBundle\Form\ProduitType', $produit);
        $editForm->handleRequest($request);

        // ceci permet d'enlever la colonne pdtRef dans l'editform
        //$editForm->remove('pdtRef');

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if($editForm->get('phProdt')->getData() != null) {
                if($produit->getPdtPhoto() != null) {
                    unlink(__DIR__.'/../../../web/uploads/produits/'.$produit->getPdtPhoto());
                    $produit->setPdtPhoto(null);
                }
            }
/*            if($editForm->get('phProdt2')->getData() != null) {
                if($produit->getPdtPhoto2() != null) {
                    unlink(__DIR__.'/../../../web/uploads/produits/'.$produit->getPdtPhoto2());
                    $produit->setPdtPhoto2(null);
                }
            }*/
            if($editForm->get('phPackag')->getData() != null) {
                if($produit->getPdtPckgPhoto() != null) {
                    unlink(__DIR__.'/../../../web/uploads/produits/'.$produit->getPdtPckgPhoto());
                    $produit->setPdtPckgPhoto(null);
                }
            }
            $produit->preUpload();

            $em->persist($produit);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'mesModifs',
                'Modification enregistrée'
            );

            return $this->redirectToRoute('produit_show', array('id' => $produit->getId()));
//            return $this->redirectToRoute('produit_index');
        }

        return $this->render('EcommerceBundle:produit:edit.html.twig', array(
            'produit' => $produit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Produit entity.
     *
     */
    public function deleteAction(Request $request, Produit $produit)
    {
        $form = $this->createDeleteForm($produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produit);
            $em->flush();
        }

        return $this->redirectToRoute('produit_index');
    }

    /**
     * Creates a form to delete a Produit entity.
     *
     * @param Produit $produit The Produit entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Produit $produit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produit_delete', array('id' => $produit->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
