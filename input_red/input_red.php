<?php
//Laravel DataBase
use WHMCS\Database\Capsule;

function input_red_config() {
    $configarray = array(
    'name' => 'INPUT RED',
    'description' => 'Sistema desenvolvido para fins de bloquear campos de edição no cadastro do cliente.',
    'version' => '0.3',
    'language' => 'portuguese-br',
    'author' => 'WHMCS.RED',
    );
    return $configarray;
}

function input_red_activate($vars) {
    //Linguagem
    $LANG = $vars['_lang'];

    //Criando nova tabela
	Capsule::schema()->create('mod_inputred',
	    function ($table) {
	        /** @var \Illuminate\Database\Schema\Blueprint $table */
	        $table->increments('id');
	        $table->string('campo');
	        $table->string('data');
	    }
	);

    //Retorno
    return array('status'=>'success','description'=>'Módulo INPUT RED ativado com sucesso!');
    return array('status'=>'error','description'=>'Não foi possível ativar o módulo de INPUT RED por causa de um erro desconhecido');
}
 
function input_red_deactivate($vars) {

    //Remover Banco de Dados
	Capsule::schema()->drop('mod_inputred');

    //Retorno
    return array('status'=>'success','description'=>'Módulo INPUT RED foi desativado com sucesso!');
    return array('status'=>'error','description'=>'Não foi possível desativar o módulo INPUT RED por causa de um erro desconhecido');
}

function input_red_output($vars){
    //Linguagem
    $LANG = $vars['_lang'];
    //Deletar 
    if($_GET['acao']=='deletar'){
    	//Verifica se a ID não veio vazia
    	if($_GET['idl']==''){
    		//Mensagem
    		echo '<div class="alert alert-danger" role="alert">'.$LANG["deletarerro"].'</div>';
    	}
    	//Se não estiver prossegue!
    	else{
    		//Removendo do banco de dados
		    Capsule::connection()->transaction(
		        function ($connectionManager)
		        {
		            /** @var \Illuminate\Database\Connection $connectionManager */
		            $connectionManager->table('mod_inputred')->WHERE('id', $_GET['idl'])->delete();
		        }
		    );

    		//Mensagem
    		echo '<div class="alert alert-success" role="alert">'.$LANG["deletarsucesso"].'</div>';
    	}
    }

    if($_GET['acao']=='editar'){
    	//Verifica se os campos são preenchidos válidos
    	if(isset($_POST['campo']) && isset($_POST['key'])){
    		//Atualizar no Banco de Dados
		    Capsule::connection()->transaction(
		        function ($connectionManager)
		        {
		            /** @var \Illuminate\Database\Connection $connectionManager */
		            $connectionManager->table('mod_inputred')->WHERE('id', $_POST['key'])->update(['campo' => $_POST['campo'],]);
		        }
		    );

    		//Mensagem
    		echo '<div class="alert alert-success" role="alert">'.$LANG["atualizarbloqueiosucesso"].'</div>';
 	   	}
 	   	//Caso não for exibe erro.
    	else{
    		//Mensagem
    		echo '<div class="alert alert-danger" role="alert">'.$LANG["atualizarbloqueioerro"].'</div>';
    	}
    }

    //Cadastrar
    if($_GET['acao']=='cadastrar'){
    	//Verifica se os campos são preenchidos válidos
    	if(isset($_POST['campo'])){
    		//Salvar no Banco de Dados
		    Capsule::connection()->transaction(
		        function ($connectionManager)
		        {
		            /** @var \Illuminate\Database\Connection $connectionManager */
		            $connectionManager->table('mod_inputred')->insert(['campo' => $_POST['campo'], 'data' => date('d/m/Y'),]);
		        }
		    );

    		//Mensagem
    		echo '<div class="alert alert-success" role="alert">'.$LANG["sucessobloqueio"].'</div>';
 	   	}
 	   	//Caso não for exibe erro.
    	else{
    		//Mensagem
    		echo '<div class="alert alert-danger" role="alert">'.$LANG["errobloqueio"].'</div>';
    	}
    }
?>
<!--Colunas-->
<div class="row">
  <!--Módulo-->
  <div class="col-md-8">
  	<!--panel inputs-->
  	<div class="panel panel-default">
  		<!--titulo panel inputs-->
	  <div class="panel-heading"><h3 class="panel-title pull-left"><i class="fa fa-ban" aria-hidden="true"></i> <?=$LANG['lista'];?></h3>   <!--Botão para nova bloqueio-->
	 	 <button class="btn btn-success pull-right" data-toggle="modal" data-target="#novo"><i class="fa fa-plus-circle" aria-hidden="true"></i> <?=$LANG['novo'];?></button> <div class="clearfix"></div></div>
	  <div class="panel-body">
	  	<div class="form-group pull-right">
		    <input type="text" class="search form-control" placeholder="<?=$LANG['busca'];?>...">
		</div>
		<span class="counter pull-right"></span>
		<table class="table table-hover table-bordered results">
		  <thead>
		    <tr>
		      <th>#</th>
		      <th class="col-md-5 col-xs-5"><?=$LANG['campo'];?></th>
		      <th class="col-md-5 col-xs-5"><?=$LANG['data'];?></th>
		      <th class="col-md-2 col-xs-2"><?=$LANG['acao'];?></th>
		    </tr>
		    <tr class="warning no-result">
		      <td colspan="5"><i class="fa fa-warning"></i> <?=$LANG['nenhum'];?></td>
		    </tr>
		  </thead>
		  <tbody>
			<?php
			foreach (Capsule::table('mod_inputred')->get() as $fieldresultado){
				$camporesultado = explode('|', $fieldresultado->campo)
			?>
		  	<tr>
		      <th scope="row"><?=$fieldresultado->id;?></th>
		      <td><?=$camporesultado[1];?></td>
		      <td><?=$fieldresultado->data;?></td>
		      <td><div class="row"><div class="col-md-6"><button type="button" class="btn btn-warning" data-toggle="modal" data-tooltip="tooltip" title="<?=$LANG['editarbloqueio'];?>" data-target="#editar-<?=$fieldresultado->id;?>"><i class="fa fa-pencil-square" aria-hidden="true"></i></button></div><div class="col-md-6"><a href="addonmodules.php?module=input_red&acao=deletar&idl=<?=$fieldresultado->id;?>" data-toggle="tooltip" title="<?=$LANG['removerbloqueio'];?>" class="btn btn-danger"><i class="fa fa-window-close" aria-hidden="true"></i></a></div></div></td>
		    </tr>
			<? } ?>
		  </tbody>
		</table>
	  </div>
	</div>
  </div>
  <!--Updates-->
  <div class="col-md-4">
  	<!--Panel Update-->
  	<div class="panel panel-default">
  	  <!--titulo update-->
	  <div class="panel-heading"><i class="fa fa-wrench" aria-hidden="true"></i> <?=$LANG['atualizacao'];?></div>
	  <!--conteudo update-->
	  <div class="panel-body">
	    <?php
        	$versao = $vars['version'];
		    $versaodisponivel = file_get_contents("http://whmcs.red/versao/input_red.txt");
		    if($versao==$versaodisponivel){
		        echo '<center><i class="fa fa-check-circle-o" aria-hidden="true"></i> '.$LANG['sucatualizacao'].'</center>';
		    }
		    else{
		    	echo '<center><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '.$LANG['erroatualizacao'].'<br/><a href="http://www.whmcs.red" class="btn btn-danger"><i class="fa fa-download" aria-hidden="true"></i> '.$LANG['baixar'].'</a></center>';
		    }

        ?>
	  </div>
	</div>
  </div>
</div>
<div class="panel-footer">Desenvolvido por <a href="http://www.whmcs.red" target="_new">WHMCS.RED</a> / <?=$LANG['creditoextra'];?>: <a data-toggle="modal" data-target="#creditos"><?=$LANG['conferirlista'];?></a></div>


<!-- Novo Bloqueio -->
<div id="novo" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-ban" aria-hidden="true"></i> <?=$LANG['novo'];?></h4>
      </div>
      	<form action="addonmodules.php?module=input_red&acao=cadastrar" method="POST">
      <div class="modal-body">
        <div class="form-group">
            <label><?=$LANG['campo'];?></label>
            <select name="campo" id="campo" class="form-control" required="required">
            	<optgroup label="<?=$LANG['campospadroes'];?>">
	            	<option value="inputFirstName|Nome">Nome</option>
	            	<option value="inputLastName|Sobrenome">Sobrenome</option>
	            	<option value="inputCompanyName|Empresa">Empresa</option>
	            	<option value="inputEmail|E-mail">E-mail</option>
	            	<option value="inputAddress1|Endereço">Endereço</option>
	            	<option value="inputAddress2|Bairro">Bairro</option>
	            	<option value="inputCity|Cidade">Cidade</option>
	            	<option value="inputState|Estado">Estado</option>
	            	<option value="inputPostcode|CEP">CEP</option>
	            	<option value="country|País">País</option>
	            	<option value="inputPhone|Telefone">Telefone</option>
	            	<option value="inputPaymentMethod|Forma de Pagamento">Forma de Pagamento</option>
	            	<option value="inputBillingContact|Contato de cobrança padrão">Contato de cobrança padrão</option>
	            </optgroup>
	            <optgroup label="<?=$LANG['campospersonalizados'];?>">
	            	<?php
	            	foreach (Capsule::table('tblcustomfields')->WHERE('type', 'client')->get() as $field) {
	            		echo '<option value="customfield'.$field->id.'|'.$field->fieldname.'">'.$field->fieldname.'</option>';
					}
	            	?>
	            </optgroup>
            </select>
        </div>
      </div>
      <div class="modal-footer">
      	<input type="submit" value="<?=$LANG['cadastrar'];?>" class="btn btn-success">
      </form>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?=$LANG['cancelar'];?></button>
      </div>
    </div>
  </div>
</div>


<?php
foreach (Capsule::table('mod_inputred')->get() as $mod){
?>
<!-- Editar -->
<div id="editar-<?=$mod->id;?>" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-ban" aria-hidden="true"></i> <?=$LANG['editarbloqueio'];?> #<?=$mod->id;?></h4>
      </div>
      	<form action="addonmodules.php?module=input_red&acao=editar" method="POST">
      	<input type="hidden" name="key" id="key" value="<?=$mod->id;?>">
      <div class="modal-body">
        <div class="form-group">
            <label><?=$LANG['campo'];?></label>
            <select name="campo" id="campo" class="form-control" required="required">
            	<optgroup label="<?=$LANG['campospadroes'];?>">
	            	<option value="inputFirstName|Nome" <? if($mod->campo=='inputFirstName|Nome'){ echo 'selected="selected"'; } ;?>>Nome</option>
	            	<option value="inputLastName|Sobrenome" <? if($mod->campo=='inputLastName|Sobrenome'){ echo 'selected="selected"'; } ;?>>Sobrenome</option>
	            	<option value="inputCompanyName|Empresa" <? if($mod->campo=='inputCompanyName|Empresa'){ echo 'selected="selected"'; } ;?>>Empresa</option>
	            	<option value="inputEmail|E-mail" <? if($mod->campo=='inputEmail|E-mail'){ echo 'selected="selected"'; } ;?>>E-mail</option>
	            	<option value="inputAddress1|Endereço" <? if($mod->campo=='inputAddress1|Endereço'){ echo 'selected="selected"'; } ;?>>Endereço</option>
	            	<option value="inputAddress2|Bairro" <? if($mod->campo=='inputAddress2|Bairro'){ echo 'selected="selected"'; } ;?>>Bairro</option>
	            	<option value="inputCity|Cidade" <? if($mod->campo=='inputCity|Cidade'){ echo 'selected="selected"'; } ;?>>Cidade</option>
	            	<option value="inputState|Estado" <? if($mod->campo=='inputState|Estado'){ echo 'selected="selected"'; } ;?>>Estado</option>
	            	<option value="inputPostcode|CEP" <? if($mod->campo=='inputPostcode|CEP'){ echo 'selected="selected"'; } ;?>>CEP</option>
	            	<option value="country|País" <? if($mod->campo=='country|País'){ echo 'selected="selected"'; } ;?>>País</option>
	            	<option value="inputPhone|Telefone" <? if($mod->campo=='inputPhone|Telefone'){ echo 'selected="selected"'; } ;?>>Telefone</option>
	            	<option value="inputPaymentMethod|Forma de Pagamento" <? if($mod->campo=='inputPaymentMethod|Forma de Pagamento'){ echo 'selected="selected"'; } ;?>>Forma de Pagamento</option>
	            	<option value="inputBillingContact|Contato de cobrança padrão" <? if($mod->campo=='inputBillingContact|Contato de cobrança padrão'){ echo 'selected="selected"'; } ;?>>Contato de cobrança padrão</option>
	            </optgroup>
	            <optgroup label="<?=$LANG['campospersonalizados'];?>">
	            	<?php
	            	foreach (Capsule::table('tblcustomfields')->WHERE('type', 'client')->get() as $field) {
	            		echo '<option value="customfield'.$field->id.'|'.$field->fieldname.'"'; if($mod->campo=='customfield'.$field->id.'|'.$field->fieldname.''){ echo 'selected="selected"'; } echo '>'.$field->fieldname.'</option>';
					}
	            	?>
	            </optgroup>
            </select>
        </div>
      </div>
      <div class="modal-footer">
      	<input type="submit" value="<?=$LANG['editar'];?>" class="btn btn-success">
      </form>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?=$LANG['cancelar'];?></button>
      </div>
    </div>
  </div>
</div>
<? } ?>
<!-- Creditos -->
<div id="creditos" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?=$LANG['creditoextra'];?></h4>
      </div>
      <div class="modal-body">
        <p>O INPUT RED vem para ajudar a ter um maior controle sobre edição de informações de campos pelos clientes em seu perfil, podendo assim o administrador bloquear qualquer campo de edição após o cadastro.<br/> O Módulo foi desenvolvido por Luciano Zanita, membro e fundador da WHMCS.RED.<br/></p>
        <br/>
        <p>Créditos extras:<br/>
        Table Search por: <a href="http://codepen.io/adobewordpress/pen/gbewLV" target="_new">http://codepen.io/adobewordpress/pen/gbewLV</a><br/><Br/>
        Confira nosso site: <a href="http://whmcs.red" target="_new">WHMCS.RED</a><br/>
        Visite nosso fórum: <a href="http://forum.whmcs.red" target="_new">FORUM.WHMCS.RED</a></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><?=$LANG["fechar"];?></button>
      </div>
    </div>

  </div>
</div>
<style type="text/css">
.panel-heading h3 {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: normal;
    width: 75%;
    padding-top: 8px;
}
.results tr[visible='false'],
.no-result{
  display:none;
}

.results tr[visible='true']{
  display:table-row;
}

.counter{
  padding:8px; 
  color:#ccc;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
  $(".search").keyup(function () {
    var searchTerm = $(".search").val();
    var listItem = $('.results tbody').children('tr');
    var searchSplit = searchTerm.replace(/ /g, "'):containsi('")
    
  $.extend($.expr[':'], {'containsi': function(elem, i, match, array){
        return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
  });
    
  $(".results tbody tr").not(":containsi('" + searchSplit + "')").each(function(e){
    $(this).attr('visible','false');
  });

  $(".results tbody tr:containsi('" + searchSplit + "')").each(function(e){
    $(this).attr('visible','true');
  });

  var jobCount = $('.results tbody tr[visible="true"]').length;
    $('.counter').text(jobCount + ' <?=$LANG['resultado'];?>');

  if(jobCount == '0') {$('.no-result').show();}
    else {$('.no-result').hide();}
		  });
});
$(document).ready(function(){
    $('[data-tooltip="tooltip"]').tooltip(); 
});
</script>
<? } ?>