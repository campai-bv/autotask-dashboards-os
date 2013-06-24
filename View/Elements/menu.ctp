<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>

			<a class="brand" href="<?php echo $this->here; ?>">
				<i class=""></i> <?php echo $sApplicationTitle; ?>
			</a>

			<?php
				if(
					'Dashboards' == $this->name
					&&
					'display' == $this->action
				) {

					echo '<span class="last-import">Last import: ';

						if( $this->Time->isToday( $sLastImportDate ) ) {
							echo 'Today at ' . $this->Time->format( 'H:i', $sLastImportDate );
						} else {
							echo $this->Time->format( 'd-m-Y \a\t H:i', $sLastImportDate );
						}

					echo '</span>';

				}

				if(
					'Dashboards' == $this->name
					&&
					'reorganize' == $this->action
				) {

					echo $this->Html->link( '<i class="icon-save"></i> Save', array(
							'plugin' => 'autotask'
						,	'controller' => 'dashboards'
						,	'action' => 'display'
						,	$this->request->data['Dashboard']['id']
					), array(
							'escape' => false
						,	'class' => 'btn btn-success pull-left'
					) );

				}
			?>

			<div class="nav-collapse collapse">

				<ul class="nav pull-right">

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-cog"></i> <b class="caret"></b></a>
						
						<ul class="dropdown-menu">
							<?php
								if(
									'Dashboards' == $this->name
									&&
									'display' == $this->action
								) {

									echo '<li class="divider"></li>';
									echo '<li class="nav-header">' . $this->request->data['Dashboard']['name'] . '</li>';

									echo '<li>';

										echo $this->Html->link(
												'<i class="icon-edit"></i> Edit'
											,	array(
														'plugin' => 'autotask'
													,	'controller' => 'dashboards'
													,	'action' => 'edit'
													,	$this->request->data['Dashboard']['id']
												)
											,	array(
														'escape' => false
												)
										);

									echo '</li>';
									echo '<li>';

										echo $this->Html->link(
												'<i class="icon-move"></i> Reorganize'
											,	array(
														'plugin' => 'autotask'
													,	'controller' => 'dashboards'
													,	'action' => 'reorganize'
													,	$this->request->data['Dashboard']['id']
												)
											,	array(
														'escape' => false
												)
										);

									echo '</li>';

								}

								if(
									'Dashboards' == $this->name
									&&
									'reorganize' == $this->action
								) {

									echo '<li class="divider"></li>';
									echo '<li class="nav-header">' . $this->request->data['Dashboard']['name'] . '</li>';

									echo '<li>';

										echo $this->Html->link(
												'<i class="icon-edit"></i> Edit'
											,	array(
														'plugin' => 'autotask'
													,	'controller' => 'dashboards'
													,	'action' => 'edit'
													,	$this->request->data['Dashboard']['id']
												)
											,	array(
														'escape' => false
												)
										);

									echo '</li>';
									echo '<li>';

										echo $this->Html->link(
												'<i class="icon-desktop"></i> View'
											,	array(
														'plugin' => 'autotask'
													,	'controller' => 'dashboards'
													,	'action' => 'display'
													,	$this->request->data['Dashboard']['id']
												)
											,	array(
														'escape' => false
												)
										);

									echo '</li>';

								}
							?>

							<li class="divider"></li>
							<li class="nav-header">Dashboards</li>
							<li>
								<?php
									echo $this->Html->link( 'List', array(
											'plugin' => 'autotask'
										,	'controller' => 'dashboards'
										,	'action' => 'index'
									) );
								?>
							</li>
							<li>
								<?php
									echo $this->Html->link( 'Add', array(
											'plugin' => 'autotask'
										,	'controller' => 'dashboards'
										,	'action' => 'add'
									) );
								?>
							</li>
							<li class="divider"></li>
							<li class="nav-header">Autotask</li>
							<li>
								<?php
									echo $this->Html->link( 'Queues', array(
											'plugin' => 'autotask'
										,	'controller' => 'queues'
									) );
								?>
							</li>
							<li>
								<?php
									echo $this->Html->link( 'Resources', array(
											'plugin' => 'autotask'
										,	'controller' => 'resources'
									) );
								?>
							</li>
							<li>
								<?php
									echo $this->Html->link( 'Ticket Statuses', array(
											'plugin' => 'autotask'
										,	'controller' => 'ticketstatuses'
									) );
								?>
							</li>
							<li>
								<?php
									echo $this->Html->link( 'Sub Issue Types', array(
											'plugin' => 'autotask'
										,	'controller' => 'subissuetypes'
									) );
								?>
							</li>
							<li class="divider"></li>
							<li>
								<?php
									echo $this->Html->link( 'Settings', array(
											'plugin' => 'autotask'
										,	'controller' => 'settings'
									) );
								?>
							</li>
						</ul>
					</li>

				</ul>

			</div><!--/.nav-collapse -->
		</div>
	</div>
</div>