<wv:adminIndex>
<div id="loginbox">

<div class="control-group normal_text"> <h3> <img  class="logo" src="@statics('images/northwest-logo.png')"/></h3></div>

<form ng-app="loginApp" ng-controller="loginCtrl" id="loginform" class="form-vertical" action="#!" method="POST">
 <div class="control-group">
   {~@csrf~}
   <div class="controls">
     <div class="main_input_box">
         <span class="add-on bg_lg"><i class="icon-user"> </i></span><input ng-model="usrname" type="text" placeholder="Username" name="username" required />
     </div>
 </div>
</div>
<div class="control-group">
 <div class="controls">
     <div class="main_input_box">
         <span class="add-on bg_ly"><i class="icon-lock"></i></span><input ng-model="ucode" type="password" placeholder="Password" name="password" required />
     </div>
 </div>
</div>
<div class="form-actions">
<span class="pull-right">
 <button ng-click="LoginUser($event)" id="adlogin-form-btn" class="btn btn-success" name="Submit">Login</button></span>

</div>
</form>

</div>
</wv:adminIndex>