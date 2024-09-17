<?php
/**
 ** Data : 02/01/2022
 ** Autor:Whilton Reis
 ** Polaris Tecnologia
 **/

class Menu extends Polar {
	
	public function montaMenu($url) {
        
        //* Carrega o ID da sessão para a váriavel
		$usuarioID = isset($_SESSION['usuarioID']) ? $_SESSION['usuarioID'] : '';  

        //* Inicio do menu estático 
        $menu  = '<div id="cssmenu">';
        $menu .= '<ul>';
        $menu .= '<li class="usuzi-font">';
        $menu .= '<a href="#""><img src="images/logo_dashboard.png"></a>';
        $menu .= '</li>';        

        //* Define se o link esta ativo
        $sel = $url == 'principal'? 'active' : '';  

        //* Monta o link principal
        $menu .= '<li class="'.$sel.'">';
        if ($this->permissao($usuarioID,'dashboard','view')) {
        	$menu .= '<a href="principal"><i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i> Principal</a>';
        } else {
        	$menu .= '<a href="principal"><i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i> Principal</a>';
        }
		$menu .= '</li>';

        //* Verifica se o usuário tem permissão para o link cadastros
		if($this->permissao($usuarioID,'cadastros','view')) {            
            //* Define se o link esta ativo
            $sel = $url == 'cadastros' ? 'active' : '';
		    $menu .= '<li class="'.$sel.'">';
		    $menu .= '<a href="#"><i class="fa fa-pencil fa-fw" aria-hidden="true"></i>Cadastros</a>';
		    $menu .= '<ul>';            

            //* Verifica se o usuário tem permissão para o link usuarios
		    if($this->permissao($usuarioID,'usuarios','view')){       
		        $menu .= '<li>';
		        $menu .= '<a href="cadastros-usuario"><i class="fa fa-caret-right fa-fw" aria-hidden="true"></i> Usuários</a>';                    
		        $menu .= '</li>';      
            }
		    $menu .= '</ul>';          
		    $menu .= '</li>';
		}

		//* Verifica se o usuário tem permissão para o link solicitações
		if($this->permissao($usuarioID,'solicitacoes','view')){

			//* Define se o link esta ativo
            $sel = $url == 'cirurgico' || $url == 'instrumental' ? 'active' : '';
		    $menu .= '<li class="'.$sel.'">';
		    $menu .= '<a href="#"><i class="fa fa-list-alt fa-fw" aria-hidden="true"></i>Solicitações</a>'; 
		    $menu .= '<ul>'; 
		    if($this->permissao($usuarioID,'cirurgico','view')){           
		        $menu .= '<li>';
		        $menu .= '<a href="solicitacoes-cirurgico"><i class="fa fa-caret-right fa-fw" aria-hidden="true"></i>Agendamento de Cirurgia</a>';                    
		        $menu .= '</li>'; 
		    }
            if($this->permissao($usuarioID,'instrumental','view')) {
                $menu .= '<li>';
                $menu .= '<a href="solicitacoes-instrumental"><i class="fa fa-caret-right fa-fw" aria-hidden="true"></i>Agendamento de Instrumental</a>';                  
                $menu .= '</li>';     
            } 
		    $menu .= '</ul>';          
		    $menu .= '</li>';
		}

		//* Verifica se o usuário tem permissão para o link solicitações
		if($this->permissao($usuarioID,'parametrizacoes','view')){

			//* Define se o link esta ativo
            $sel = $url == 'permissao' ? 'active' : '';
		    $menu .= '<li class="'.$sel.'">';
		    $menu .= '<a href="#"><i class="fa fa-cogs fa-fw" aria-hidden="true"></i>Parametrizações</a>'; 
		    $menu .= '<ul>'; 

            if($this->permissao($usuarioID,'permissao','view')) {
                $menu .= '<li>';
                $menu .= '<a href="parametrizacoes-permissao"><i class="fa fa-caret-right fa-fw" aria-hidden="true"></i>Permissões</a>';                  
                $menu .= '</li>';     
            } 
		    $menu .= '</ul>';          
		    $menu .= '</li>';
		}

		//* Verifica se o usuário tem permissão para o link solicitações
		if($this->permissao($usuarioID,'configuracao','view')){

			//* Define se o link esta ativo
            $sel = $url == 'configuracao' ? 'active' : '';
		    $menu .= '<li class="'.$sel.'">';
		    $menu .= '<a href="configuracao"><i class="fa fa-cog fa-fw" aria-hidden="true"></i>Configuracões</a>';   
		    $menu .= '</li>';
		}
		if($this->permissao($usuarioID,'configuracao','view')){

			//* Define se o link esta ativo
            $sel = $url == 'incidente' ? 'active' : '';
		    $menu .= '<li class="'.$sel.'">';
		    $menu .= '<a href="incidente"><i class="fa fa-cog fa-fw" aria-hidden="true"></i>Incidentes Cirurgicos</a>';   
		    $menu .= '</li>';
		}

		//* Verifica se o usuário tem permissão para o link telas
		if($this->permissao($usuarioID,'telas','view')){

		    //* Define se o link esta ativo
            $sel = $url == 'telas' ? 'active' : '';
		    $menu .= '<li class="'.$sel.'">';
		    $menu .= '<a href="#"><i class="fa fa-window-restore fa-fw" aria-hidden="true"></i> Telas</a>';
		    $menu .= '<ul>';
		    if($this->permissao($usuarioID,'londrina','view')) {
			    $menu .= '<li>';
			    $menu .= '<a href="tela-londrina"><i class="fa fa-caret-right fa-fw" aria-hidden="true"></i> Londrina</a>';                    
			    $menu .= '</li>';
			}

			if($this->permissao($usuarioID,'maringa','view')) {
			    $menu .= '<li>';
			    $menu .= '<a href="tela-maringa"><i class="fa fa-caret-right fa-fw" aria-hidden="true"></i> Maringá</a>';                    
			    $menu .= '</li>'; 
			} 

			if($this->permissao($usuarioID,'cascavel','view')) {
			    $menu .= '<li>';
			    $menu .= '<a href="tela-cascavel"><i class="fa fa-caret-right fa-fw" aria-hidden="true"></i> Cascavel</a>';                    
			    $menu .= '</li>'; 
			}       
		    $menu .= '</ul>';           
		    $menu .= '</li>';
		}

		//* Verifica se o usuário tem permissão para o link agenda
		if($this->permissao($usuarioID,'agenda','view')){

			//* Instancia o objeto principal
			$principal = new Principal();

		    //* Define se o link esta ativo
            $sel = $url == 'agenda' ? 'active' : '';
		    $menu .= '<li class="'.$sel.'">';
		    $menu .= '<a href="principal-agenda"><i class="fa fa-calendar fa-fw" aria-hidden="true"></i> Agenda</a>';
		    $menu .= '</li>';
		    $menu .= '<li class="'.$sel.'" style="float: right;">';
		    $menu .= '<a href="principal-agenda">';
		    $menu .= '<span id="notificacoes">'.$principal->Notificacoes().'</span>';
		    $menu .= '<span class="badge" id="bell">';
		    $menu .= '<i class="fa fa-bell  fa-fw"></i>';
		    $menu .= '</span>'; 
		    $menu .= '</a>';
		    $menu .= '</li>';
		}        
		
        //* Final do menu estático 
		$menu .= '<li style="float: right;">';
		$menu .= '<a href="logout">';
		$menu .= '<span class="badge" id="bell">';
		$menu .= '<i class="fa fa-sign-out fa-fw" aria-hidden="true"></i> Sair</span></a>';
		$menu .= '</li>';
		$menu .= '</ul>';
		$menu .= '</div>';

		return $menu;		      
	
	}
}