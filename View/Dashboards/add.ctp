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
	<li>
		<?php
			echo $this->Html->link( 'Dashboards', array(
					'plugin' => 'autotask'
				,	'controller' => 'dashboards'
				,	'action' => 'index'
			), array(
					'escape' => false
			) );
		?> <span class="divider">/</span>
	</li>
	<li class="active">
		<?php
			if( 'add' == $this->action) {
				echo 'Add';
			} else {
				echo $this->request->data['Dashboard']['name'];
			}
		?>
	</li>
</ul>

<?php
	echo $this->Form->create( 'Autotask.Dashboard', array(
			'class' => 'form-horizontal'
		,	'inputDefaults' => array(
					'div' => array(
							'class' => 'control-group input'
					)
				,	'label' => array(
							'class' => 'control-label'
					)
				,	'between' => '<div class="controls">'
				,	'after' => '</div>'
				,	'class' => 'span2'
			)
	) );

	if( 'edit' == $this->action ) {
		echo $this->Form->input( 'Dashboard.id' );
	}

	echo $this->Form->input( 'Dashboard.name' );
	echo $this->Form->input( 'Dashboard.slug' );

	echo $this->Form->input( 'Dashboardqueue.id', array(
			'options' => $aQueues['options']
		,	'selected' => $aQueues['selected']
		,	'multiple' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkboxes-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Queues'
			)
	) );

	echo $this->Form->input( 'Dashboardticketstatus.id', array(
			'options' => $aTicketstatuses['options']
		,	'selected' => $aTicketstatuses['selected']
		,	'multiple' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkboxes-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Ticket Statuses'
			)
	) );

	echo $this->Form->input( 'Dashboardresource.id', array(
			'options' => $aResources['options']
		,	'selected' => $aResources['selected']
		,	'multiple' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkboxes-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Resources'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_kill_rate', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show Kill Rate'
				,	'title' => 'The kill rate is a representation of the amount of tickets that are <strong>completed today</strong> compared to all tickets <strong>created today</strong>.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_rolling_week', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show Rolling Week (Graph)'
				,	'title' => 'The rolling week shows a weekly history of the amount of tickets that are <strong>completed by day</strong> compared to all tickets <strong>created by day</strong>.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_rolling_week_bars', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show Rolling Week (Bars)'
				,	'title' => 'The rolling week shows a weekly history of the amount of tickets that are <strong>completed by day</strong> compared to all tickets <strong>created by day</strong>.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_queue_health', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show Queue Health'
				,	'title' => 'The queue health shows a weekly history of the average days tickets are open per queue.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_accounts', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show Accounts'
				,	'title' => 'A list of the top accounts (max 10), sorted by most tickets (desc). Depends on the queues and resources you select.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_queues', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show Queues'
				,	'title' => 'A list of the top queues (max 10), sorted by average days a ticket is open (asc). Depends on the queues you select.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_resources', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show Resources'
				,	'title' => 'A list of resources (max 10), sorted by amount of tickets they\'ve completed today (desc). Depends on the queues and resources you select.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_unassigned', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show Unassigned'
				,	'title' => 'The amount of unassigned tickets. Depends on the queues and resources you select.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_missing_issue_type', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show Missing Issue Type'
				,	'title' => 'The amount of tickets that are missing a sub-issue type. Depends on the queues and resources you select.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_sla_violations', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show # SLA violations'
				,	'title' => 'The amount of tickets that are violating their associated SLA.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_tickets_top_x', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show Top X Tickets'
				,	'title' => 'The latest tickets created within Autotask.'
			)
	) );

	echo $this->Form->input( 'Dashboard.show_clock', array(
			'type' => 'checkbox'
		,	'div' => array(
					'class' => 'control-group input checkbox-container'
			)
		,	'class' => 'checkbox'
		,	'label' => array(
					'class' => 'control-label'
				,	'text' => 'Show the clock'
				,	'title' => 'Do you have the time?'
			)
	) );

	if( 'add' == $this->action) {

		echo $this->Form->submit( 'Add', array(
				'class' => 'btn btn-success'
		) );

	} else {

		echo $this->Form->submit( 'Save', array(
				'class' => 'btn btn-success'
		) );

	}

	echo $this->Form->end();
?>

<script>
	$(function() {

		$( '.control-label' ).tooltip({
				placement: 'right'
			,	html: true
		});

	});
</script>