<?php

/**
 * IndexController
 */
class IndexController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateAfter('main');
        Phalcon\Tag::setTitle('High performance PHP framework');
        parent::initialize();
    }

    public function indexAction()
    {
    }

    public function docsAction($name=null){
        $name = $this->dispatcher->getParam("name");
        $name = $this->filter->sanitize($name, "string");
        if($name){
            $this->response->redirect("http://docs.phalconphp.com/en/latest/reference/".$name.".html", true);
        } else {
            $this->response->redirect("http://docs.phalconphp.com/", true);
        }
    }

    public function subscribeAction()
    {
        $email = $this->request->getPost('email', 'email');
        if (!$email) {
            $this->flash->error('Please provide a valid email');
            return $this->dispatcher->forward(array('action' => 'index'));
        }

        $exists = Subscribers::count("email='$email'");
        if ($exists==false) {
            $subscriber = new Subscribers();
            $subscriber->email = $email;
            $subscriber->created_at = new Phalcon\Db\RawValue('now()');
            if ($subscriber->save()==false) {
                foreach ($subscriber->getMessages() as $message) {
                    $this->flash->error("At this moment you can\'t subscribe");
                }
            } else {
                $this->flash->success('Thanks for subscribing!');
            }
        } else {
            $this->flash->success("You are already subscribed!");
        }

        return $this->dispatcher->forward(array('controller' => 'index', 'action' => 'index'));
    }
}
