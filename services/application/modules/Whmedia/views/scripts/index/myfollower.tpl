<style type="text/css">
	#whmedia_follow_table{
		min-width: 450px;
		max-height: 350px;
		overflow-y: auto;
	}
	#whmedia_follow_table td{
		text-align: center !important;
		padding: 8px 2px;
	}
</style>

<div id="whmedia_follow_table">
	<table cellpadding="0" cellspacing="0" width="100%">
		<?php $ctr = 1; $cntCtr = 1; $total = count($this->followers); ?>
		<?php foreach ($this->followers as $follow): ?>
			<?php
				$followArray = $follow->toArray();
				$user = $this->userApi->findRow($followArray["follower_id"]);
			?>
			<?php echo ($ctr == 1) ? "<tr>" : ""; ?>
			<td align="center" width="25%">
				<a onclick="parent.location.href='<?php echo $user->getHref(); ?>'" href='javascript:void(0)'>
					<?php echo $this->itemPhoto($user->getOwner(), 'thumb.icon'); ?> <br/>	
					<?php echo $user->getTitle(); ?>
				</a>
			</td>
			<?php if($ctr == 4 || $cntCtr == $total){ echo "</tr>"; $ctr = 0; }; ?>
		<?php $ctr++; $cntCtr++ ?>
		<?php endforeach; ?>
	</table>
</div>
