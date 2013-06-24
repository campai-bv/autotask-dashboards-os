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
	<li class="active">Queues</li>
</ul>

<table class="table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if( !empty( $aQueues ) ) {

				foreach ( $aQueues as $iKey => $aQueue ) {

					echo '<tr>';

						echo '<td>';
							echo $aQueue['Queue']['id'];
						echo '</td>';

						echo '<td>';

							$sName = '<i>Unspecified queue name</i>';

							if( !empty( $aQueue['Queue']['name'] ) ) {
								$sName = $aQueue['Queue']['name'];
							}

							echo $this->Html->link( $sName, array(
									'plugin' => 'autotask'
								,	'controller' => 'queues'
								,	'action' => 'edit'
								,	$aQueue['Queue']['id']
							), array(
									'escape' => false
							) );

						echo '</td>';

					echo '</tr>';

				}

			}

		?>
	</tbody>
</table>