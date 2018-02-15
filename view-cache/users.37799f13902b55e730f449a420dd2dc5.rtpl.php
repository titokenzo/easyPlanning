<?php if(!class_exists('Rain\Tpl')){exit;}?><!-- page content -->
<div class="right_col" role="main">
	<div class="">
		<div class="page-title">
			<div class="title_left">
				<h3>Usuários</h3>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<div class="box-header">
							<a href="/users/create" class="btn btn-success">Cadastrar novo usuário</a>
			            </div>
						<div class="clearfix"></div>
					</div>
					<div class="x_content">
						<table id="datatable" class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Nome</th>
									<th>E-mail</th>
									<th>Login</th>
									<th style="width: 60px">Admin</th>
									<th style="width: 140px">&nbsp;</th>
								</tr>
							</thead>

							<tbody>
								<?php $counter1=-1;  if( isset($users) && ( is_array($users) || $users instanceof Traversable ) && sizeof($users) ) foreach( $users as $key1 => $value1 ){ $counter1++; ?>
								<tr>
				                    <td><?php echo htmlspecialchars( $value1["person_name"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
				                    <td><?php echo htmlspecialchars( $value1["person_email"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
				                    <td><?php echo htmlspecialchars( $value1["user_login"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td/>
				                    <td><?php if( $value1["user_isadmin"] == 1 ){ ?>Sim<?php }else{ ?>Não<?php } ?></td>
				                    <td>
				                      <a href="/users/<?php echo htmlspecialchars( $value1["user_id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Editar</a>
				                      <a href="/users/<?php echo htmlspecialchars( $value1["user_id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>/delete" onclick="return confirm('Deseja realmente excluir este registro?')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Excluir</a>
				                    </td>							
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<!-- /page content -->
