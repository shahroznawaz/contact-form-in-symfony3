<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

use AppBundle\Entity\Contact;

class DefaultController extends Controller
{ 
     /**
     * @Route("/form", name="homepage")
     */
   public function createAction(Request $request)
    {
        $contact = new Contact;
      
      # Add form fields
        $form = $this->createFormBuilder($contact)
        ->add('name', TextType::class, array('label'=> 'name', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
        ->add('email', TextType::class, array('label'=> 'email','attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
        ->add('subject', TextType::class, array('label'=> 'subject','attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
        ->add('message', TextareaType::class, array('label'=> 'message','attr' => array('class' => 'form-control')))
        ->add('Save', SubmitType::class, array('label'=> 'submit', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-top:15px')))
        ->getForm();
        
      # Handle form and recaptcha response
        $form->handleRequest($request);
          
      # check if form is submitted and Recaptcha response is success
        if($form->isSubmitted() &&  $form->isValid()){
            $name = $form['name']->getData();
            $email = $form['email']->getData();
            $subject = $form['subject']->getData();
            $message = $form['message']->getData();
            
            
      # set form data   
            $contact->setName($name);
            $contact->setEmail($email);          
            $contact->setSubject($subject);     
            $contact->setMessage($message);                
            
       # finally add data in database
            $sn = $this->getDoctrine()->getManager();      
            $sn -> persist($contact);
            $sn -> flush();

          $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom('shahroznawaz156@gmail.com')
                ->setTo($email)
                ->setBody($this->renderView('default/sendemail.html.twig',array('name' => $name)),'text/html');

          $this->get('mailer')->send($message);



















          //  $this->get('session')->getFlashBag()->add('notice', 'message sent');

          //   //  $request->getSession()
          //   // ->getFlashBag()
          //   // ->add('success', 'Your message is successfully sent!');
            
          //   // $this->addFlash(
          //   //     'notice',
          //   //     'Your message is successfully sent'
          //   // );
            return $this->redirectToRoute('homepage');                
      } 
            
        return $this->render('default/form.html.twig', array(
            'form' => $form->createView()  
        ));
    }
    
}