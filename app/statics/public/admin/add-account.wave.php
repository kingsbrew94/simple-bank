<wv:adminIndex>
    <wv:comp.admin.header/>
    <wv:comp.admin.sidebar/>
    <div id="content">
		<div id="content-header">
			<div id="breadcrumb"> <a href="@url(':view_acc')" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="@url(':add_acc')" class="current"> Create Account </a> </div>
			<h1>Create New Account</h1>
		</div>
		<div class="container-fluid">
			<hr>
			<div class="row-fluid">
				<div class="span6">
					<div class="widget-box">
						<div class="widget-title"> <span class="icon"> <i class="icon-align-justify"></i> </span>
							<h5>Account-info</h5>
						</div>
						<div class="widget-content nopadding">
							<form action="@url(':create_cus')" method="POST" enctype="multipart/form-data" class="form-horizontal">
								<div class="control-group">
									<label class="control-label">First Name:</label>
									<div class="controls">
										<input type="text" class="span11" name="firstName" value="{~$request['firstName']??''~}" />
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Last Name:</label>
									<div class="controls">
										<input type="text" class="span11"  name="lastName" value="{~$request['lastName']??''~}"/>
									</div>
								</div>
                                <div class="control-group">
									<label class="control-label">Date of Birth:</label>
									<div class="controls">
										<input type="date" class="span11 datepicker" name="dob" value="{~$request['dob']??''~}"/>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Phone:</label>
									<div class="controls">
										<input type="text" class="span11"  name="phoneNum" value="{~$request['phoneNum']??''~}"/>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Email:</label>
									<div class="controls">
										<input type="text" class="span11" name="email" value="{~$request['email']??''~}"/>
									</div>
								</div>
                                <div class="control-group">
									<label class="control-label">Password:</label>
									<div class="controls">
										<input type="password" class="span11" name="password" value="{~$request['password']??''~}"/>
									</div>
								</div>
                                <div class="control-group">
									<label class="control-label">Confirm Password:</label>
									<div class="controls">
										<input type="password" class="span11" name="confirmPassword" value="{~$request['confirmPassword']??''~}"/>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Zip Code:</label>
									<div class="controls">
										<input type="text" class="span11" name="zipCode" value="{~$request['zipCode']??''~}"/>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Gender:</label>
									<div class="controls">
                                        <select class="span11" name="gender" value="{~$request['gender']??''~}">
											<option value="sg">--Select Gender--</option>
											<option value="M">Male</option>
											<option value="F">Female</option>
										</select>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Occupation:</label>
									<div class="controls">
										<input type="text" class="span11" name="occupation" value="{~$request['occupation']??''~}"/>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Address:</label>
									<div class="controls">
										<textarea class="span11" name="address">{~$request['address']??''~}</textarea>
									</div>
								</div>

								<div class="control-group">
									<label class="control-label">State:</label>
									<div class="controls">
										<input type="text" class="span11" name="state" value="{~$request['state']??''~}"/>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">City:</label>
									<div class="controls">
										<input type="text" class="span11" name="city" value="{~$request['city']??''~}"/>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Country:</label>
									<div class="controls">
										<input type="text" class="span11" name="country" value="{~$request['country']??''~}"/>
									</div>
								</div>
                                <div class="control-group">
									<label class="control-label">Account Number:</label>
									<div class="controls">
										<input type="text" class="span11" name="accNumber" value="{~$request['accNumber']??''~}"/>
									</div>
								</div>
                                <div class="control-group">
									<label class="control-label">Account PIN:</label>
									<div class="controls">
										<input type="text" class="span11" name="pin" value="{~$request['pin']??''~}"/>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Account Status:</label>
									
									<div class="controls">
										<select class="span11" name="accStatus" value="{~$request['accStatus']??''~}">
											<option value="active">Active</option>
											<option value="notactive">Inactive</option>
										</select>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Type of Account:</label>
									<div class="controls">
                                        <select class="span11" name="accType" value="{~$request['accType']??''~}">
											<option value="stoa">--Select Type Of Account--</option>
											<option value="PERSONAL">Personal</option>
											<option value="BUSINESS">Business</option>
										</select>
									</div>
								</div>
                                <div class="control-group">
									<label class="control-label">State of Account:</label>
									<div class="controls">
                                        <select class="span11" name="accTypeType" value="{~$request['accTypeType']??''~}">
											<option value="ssoa">--Select State Of Account--</option>
											<option value="SAVINGS">Savings</option>
											<option value="CURRENT">Current</option>
										</select>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Account Currency:</label>
									<div class="controls">
										<select class="span11" name="accCurrency" value="{~$request['accCurrency']??''~}">
											<option value="DOLLAR">DOLLARS ( $ )</option>
											<option value="EURO">EURO ( € )</option>
											<option value="POUNDS">POUNDS ( £ )</option>
											<option value="YUAN">YUAN ( ¥ )</option>
										</select>
									</div>
								</div>
                                {~@csrf~}
								<div class="control-group">
									<label class="control-label">Upload Pic ID:</label>
									<div class="controls">
										<input type="file"  class="span11" name="customerImage"/>
									</div>
								</div>
								<div class="control-group">

									<div class="controls">
										<button type="submit" class="btn btn-success">CREATE ACCOUNT</button>
									</div>
								</div>


							</form>
						</div>
					</div>


				</div>

			</div>

		</div>
	</div>
	<!--Footer-part-->
	<div class="row-fluid">
		<div id="footer" class="span12">  </div>
	</div>
</wv:adminIndex>