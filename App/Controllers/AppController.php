<?php 

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action{

    public function timeline(){

            $this->validaAutenticar();
            
            $tweet = Container::getModel('tweet');

            $tweet->__set('id_usuario',$_SESSION['id']);

           

            $total_registros_pagina = 10;
            $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

            $deslocamento = ($pagina - 1) * $total_registros_pagina;

            $this->view->pagina_ativa = $pagina;

            $this->view->tweets = $tweet->getPorPagina($total_registros_pagina,$deslocamento);

            $total_tweets = $tweet->getTotalRegistros();
            $this->view->total_de_paginas = ceil($total_tweets['total']/$total_registros_pagina);



            $usuario = Container::getModel('Usuario');

            $usuario->__set('id',$_SESSION['id']);
            
            $this->view->info_usuario = $usuario->getInfoUsuario();
            $this->view->total_tweets = $usuario->getTotalTweets();
            $this->view->total_seguindo = $usuario->getTotalSeguindo();
            $this->view->total_seguidores = $usuario->getTotalSeguidores();

            $this->render('timeline');
        
    }

    public function tweet(){

        $this->validaAutenticar();   
        
            $tweet = Container::getModel('Tweet');

            $tweet->__set('tweet',$_POST['tweet']);
            $tweet->__set('id_usuario',$_SESSION['id']);

            $tweet->salvar();

            header('Location: /timeline');

    }
    
    public function validaAutenticar(){

        session_start();

        if(!isset($_SESSION['id']) || $_SESSION['id'] =='' || $_SESSION['nome'] == '' ||  !isset($_SESSION['nome'])){

            header('Location: /?login=erro');
        }

    }

    public function quemSeguir(){

        $this->validaAutenticar();

        $this->view->usuarios = array();
        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        if($pesquisarPor != ''){
            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome',$pesquisarPor);
            $usuario->__set('id',$_SESSION['id']);
            $this->view->usuarios = $usuario->getAll();
        }

        $this->render('quemSeguir');
    }

    public function acao(){
        $this->validaAutenticar();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id',$_SESSION['id']);

        if($acao == 'seguir'){

            $usuario->seguirUsuario($id_usuario);
        }else if($acao == 'deixar_de_seguir'){

            $usuario->deixarSeguirUsuario($id_usuario);
        }

        header('Location: /quem_seguir');
    }

    public function apagar_tweet(){
        $this->validaAutenticar();

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id',$_GET['tweet_deletado']);
        $tweet->deleteTweet();

        header('Location: /timeline');
    }

   

}    
?>