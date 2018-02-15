<?php if(!class_exists('Rain\Tpl')){exit;}?><!-- page content -->
<div class="right_col" role="main">
	<div class="">
		<div class="page-title">
			<div class="title_left">
				<h3>Usuários</h3>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="x_panel">
			<div class="x_title">
				<h2>Atualização de Usuário</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<form role="form" action="/users/<?php echo htmlspecialchars( $user["user_id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" method="post">
					<div class="box-body">
						<div class="form-group">
							<label for="person_name">Nome *</label> <input type="text"
								class="form-control" id="person_name" name="person_name"
								placeholder="Digite o nome" value="<?php echo htmlspecialchars( $user["person_name"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" required />
						</div>
						<div class="form-group">
							<label for="user_login">Login *</label> <input type="text"
								class="form-control" id="user_login" name="user_login"
								placeholder="Digite o login" value="<?php echo htmlspecialchars( $user["user_login"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" required />
						</div>
						<div class="form-group">
							<label for="person_phone">Telefone</label> <input type="tel"
								class="form-control" id="person_phone" name="person_phone"
								placeholder="Digite o telefone" value="<?php echo htmlspecialchars( $user["person_phone"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
						</div>
						<div class="form-group">
							<label for="person_email">E-mail *</label> <input type="email"
								class="form-control" id="person_email" name="person_email"
								placeholder="Digite o e-mail" value="<?php echo htmlspecialchars( $user["person_email"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"
								required />
						</div>
						<div class="checkbox">
							<label> <input type="checkbox" name="user_isadmin" value="1"
								<?php if( $user["user_isadmin"] == 1 ){ ?>checked<?php } ?>> Acesso de
								Administrador
							</label>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-primary">Salvar</button><button type="button" class="btn btn-secondary" onclick="history.back()">Voltar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /page content -->
