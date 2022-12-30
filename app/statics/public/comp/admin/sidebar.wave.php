<div id="sidebar"><a href="#" class="visible-phone"><i class="icon icon-home"></i> View Accounts</a>
	<ul>
		<li class="{~$page === 'dashboard' ? 'active' : ''~}"><a href="@url(':view_acc')"><i class="icon icon-home"></i> <span>View Accounts</span></a> </li>
		<li class="{~$page === 'add' ? 'active' : ''~}"> <a href="@url(':add_acc')"><i class="icon icon-signal"></i> <span>Add Account  </span></a> </li>
		<li class="{~$page === 'credit' ? 'active' : ''~}"> <a href="@url(':credit')"><i class="icon icon-inbox"></i> <span>Credit/Debit Account</span></a> </li>
		<li class="{~$page === 'viewtransactions' ? 'active' : ''~}"> <a href="@url(':view_tranx')"><i class="icon icon-inbox"></i> <span>View Transactions</span></a> </li>
        <li class="{~$page === 'viewtrans' ? 'active' : ''~}"> <a href="@url(':view_transfer')"><i class="icon icon-inbox"></i> <span>View Transfers</span></a> </li>
		<li class="{~$page === 'accesslogs' ? 'active' : ''~}"> <a href="@url(':access_logs')"><i class="icon icon-inbox"></i> <span>Access Logs</span></a> </li>
		<li class="{~$page === 'changepass' ? 'active' : ''~}"> <a href="@url(':change_pass')"><i class="icon icon-inbox"></i> <span>Change Password</span></a> </li>
	</ul>
</div>