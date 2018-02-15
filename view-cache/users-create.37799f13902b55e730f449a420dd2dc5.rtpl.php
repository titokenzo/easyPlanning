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
				<h2>Cadastro de Usuário</h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<form role="form" action="/users/create" method="post">
					<div class="box-body">
						<div class="form-group">
							<label for="person_name">Nome *</label> <input type="text"
								class="form-control" id="person_name" name="person_name"
								placeholder="Digite o nome" required />
						</div>
						<div class="form-group">
							<label for="user_login">Login *</label> <input type="text"
								class="form-control" id="user_login" name="user_login"
								placeholder="Digite o login" required />
						</div>
						<div class="form-group">
							<label for="person_phone">Telefone</label> <input type="tel"
								class="form-control" id="person_phone" name="person_phone"
								placeholder="Digite o telefone" required />
						</div>
						<div class="form-group">
							<label for="person_email">E-mail *</label> <input type="email"
								class="form-control" id="person_email" name="person_email"
								placeholder="Digite o e-mail">
						</div>
						<div class="form-group">
							<label for="user_password">Senha *</label> <input type="password"
								class="form-control" id="user_password" name="user_password"
								placeholder="Digite a senha" required />
						</div>
						<div class="checkbox">
							<label> <input type="checkbox" name="user_isadmin" value="1">
								Acesso de Administrador
							</label>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-success">Cadastrar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /page content -->
