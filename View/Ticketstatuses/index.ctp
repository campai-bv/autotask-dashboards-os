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
	<li class="active">Ticket Statuses</li>
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
			if( !empty( $aTicketstatuses ) ) {

				foreach ( $aTicketstatuses as $iKey => $aTicketstatus ) {
					
					echo '<tr>';

						echo '<td>';
							echo $aTicketstatus['Ticketstatus']['id'];
						echo '</td>';

						echo '<td>';

							$sName = '<i>Unspecified ticket status name</i>';

							if( !empty( $aTicketstatus['Ticketstatus']['name'] ) ) {
								$sName = $aTicketstatus['Ticketstatus']['name'];
							}

							echo $this->Html->link( $sName, array(
									'plugin' => 'autotask'
								,	'controller' => 'ticketstatuses'
								,	'action' => 'edit'
								,	$aTicketstatus['Ticketstatus']['id']
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