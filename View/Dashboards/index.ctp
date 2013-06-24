<ul class="breadcrumb">
	<li>
		<?php
			echo $this->Html->link( '<i class="icon-home"></i>', array(
					'plugin' => 'autotask'
				,	'controller' => 'dashboards'
				,	'action' => 'index'
			), array(
					'escape' => false
			) );
		?> <span class="divider">/</span>
	</li>
	<li class="active">Dashboards</li>
</ul>

<table class="table">
	<thead>
		<tr>
			<th>Name</th>
			<th>Slug</th>
			<th>Queues</th>
			<th>Resources</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			if( !empty( $aDashboards ) ) {

				foreach ( $aDashboards as $iKey => $aDashboard ) {

					echo '<tr>';

						echo '<td>';

							echo $this->Html->link( $aDashboard['Dashboard']['name'], array(
									'plugin' => 'autotask'
								,	'controller' => 'dashboards'
								,	'action' => 'edit'
								,	$aDashboard['Dashboard']['id']
							) );
							echo ' ';
							echo  $this->Html->link( '<i class="icon-share"></i>', '/' . $aDashboard['Dashboard']['slug'], array(
									'escape' => false
							) );

						echo '</td>';

						echo '<td>';
							echo $aDashboard['Dashboard']['slug'];
						echo '</td>';

						echo '<td>';

							if( !empty( $aDashboard['Dashboardqueue'] ) ) {

								echo '<ul>';
									foreach ( $aDashboard['Dashboardqueue'] as $aQueue ) {
										echo '<li>' . $this->Html->link( $aQueue['Queue']['name'], array(
												'plugin' => 'autotask'
											,	'controller' => 'queues'
											,	'action' => 'edit'
											,	$aQueue['Queue']['id']
										) ) . '</li>';
									}
								echo '</ul>';

							}

						echo '</td>';

						echo '<td>';

							if( !empty( $aDashboard['Dashboardresource'] ) ) {

								echo '<ul>';

									foreach ( $aDashboard['Dashboardresource'] as $aResource ) {

										echo '<li>' . $this->Html->link( $aResource['Resource']['name'], array(
												'plugin' => 'autotask'
											,	'controller' => 'resources'
											,	'action' => 'edit'
											,	$aResource['Resource']['id']
										) ) . '</li>';

									}

								echo '</ul>';

							}

						echo '</td>';

						echo '<td>';

							echo $this->Html->link( 'Delete', array(
									'plugin' => 'autotask'
								,	'controller' => 'dashboards'
								,	'action' => 'delete'
								,	$aDashboard['Dashboard']['id']
							) );

						echo '</td>';

					echo '</tr>';

				}

			}
		?>
	</tbody>
</table>

<?php
	if( empty( $aDashboards ) ) {

		echo $this->Html->link( '<i class="icon-plus-sign"></i> Create your first dashboard!', array(
				'plugin' => 'autotask'
			,	'controller' => 'dashboards'
			,	'action' => 'add'
		), array(
				'escape' => false
			,	'class' => 'btn btn-success'
		) );

	}