<wv:adminIndex>
    <wv:comp.admin.header/>
    <wv:comp.admin.sidebar/>
    <div id="content">
		<div id="content-header">
			<div id="breadcrumb"> <a href="@url(':view_acc')" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="@url(':view_tranx')" class="current"> View Transactions </a> </div>
			<h1>VIEW TRANSACTIONS</h1>
		</div>
		<div class="container-fluid">
			<hr>
			<div class="row-fluid">
				<div class="span12">

					<table class="table table-bordered table-striped" >
						<thead>
							<tr>
								<th>#</th>

								<th>Account Name</th>
								<th>Transaction Type</th>
								<th>Amount</th>
								<th>Date</th>
								<th>Description</th>
								<td bgcolor="#006600">&nbsp;</td>
							</tr>
						</thead>
                        @each($transactions as $key => $data):
                            <tr>
                                <td bgcolor="#D9FFD9">{~$key+1~}</td>
                                <td bgcolor="#D9FFD9">{~$data->firstName.' '.$data->lastName~}</td>
                                <td bgcolor="#D9FFD9">{~$data->tranType~}</td>
                                <td bgcolor="#D9FFD9">{~$data->amount~}</td>
                                <td bgcolor="#D9FFD9">{~dateQuery($data->dateOfTran,'D, d M, Y - h:m A')~}</td>
                                <td bgcolor="#D9FFD9">{~$data->tranDescription??''~}</td>
                                <td bgcolor="#D9FFD9">
                                    <b>
                                        <a class="btn btn-danger"  onclick="return confirm('Are you sure you want to delete this Transaction?');" href="@url(':delTransaction')?tranId={~$data->tranHist~}">Delete</a>
                                    </b>
                                </td>
                            </tr>
                        @endeach
					</table>
				</div>

			</div>

		</div>

	</div>
	<!--Footer-part-->
	<div class="row-fluid">
		<div id="footer" class="span12">  </div>
	</div>
	<!--end-Footer-part-->
</wv:adminIndex>