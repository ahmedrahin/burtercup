<div class="header-dashboard">
	<div class="wrap">
		<div class="header-left">
			<a href="{{ route('dashboard') }}">
				<img class="" id="logo_header_mobile" alt="" src="{{ asset($system_settings->logo) }}" data-light="{{ asset($system_settings->logo) }}" data-dark="{{ asset($system_settings->dark_logo) }}" data-width="154px" data-height="52px" data-retina="images/logo/logo@2x.png">
			</a>
			<div class="button-show-hide">
				<i class="icon-menu-left"></i>
			</div>
			<form class="form-search flex-grow">
				<fieldset class="name">
					<input type="text" placeholder="Search here..." class="show-search"  tabindex="2" value="" aria-required="true" required="">
				</fieldset>
				<div class="button-submit">
					<button class="" type="submit"><i class="icon-search"></i></button>
				</div>
				<div class="box-content-search" id="box-content-search">
					<ul class="mb-24">
						<li class="mb-14">
							<div class="body-title">Top selling product</div>
						</li>
						<li class="mb-14">
							<div class="divider"></div>
						</li>
						<li>
							<ul>
								<li class="product-item gap14 mb-10">
									<div class="image no-bg">
										<img src="images/products/17.png" alt="">
									</div>
									<div class="flex items-center justify-between gap20 flex-grow">
										<div class="name">
											<a href="product-list.html" class="body-text">Dog Food Rachael Ray Nutrish®</a>
										</div>
									</div>
								</li>
								<li class="mb-10">
									<div class="divider"></div>
								</li>
								<li class="product-item gap14 mb-10">
									<div class="image no-bg">
										<img src="images/products/18.png" alt="">
									</div>
									<div class="flex items-center justify-between gap20 flex-grow">
										<div class="name">
											<a href="product-list.html" class="body-text">Natural Dog Food Healthy Dog Food</a>
										</div>
									</div>
								</li>
								<li class="mb-10">
									<div class="divider"></div>
								</li>
								<li class="product-item gap14">
									<div class="image no-bg">
										<img src="images/products/19.png" alt="">
									</div>
									<div class="flex items-center justify-between gap20 flex-grow">
										<div class="name">
											<a href="product-list.html" class="body-text">Freshpet Healthy Dog Food and Cat</a>
										</div>
									</div>
								</li>
							</ul>
						</li>
					</ul>
					<ul class="">
						<li class="mb-14">
							<div class="body-title">Order product</div>
						</li>
						<li class="mb-14">
							<div class="divider"></div>
						</li>
						<li>
							<ul>
								<li class="product-item gap14 mb-10">
									<div class="image no-bg">
										<img src="images/products/20.png" alt="">
									</div>
									<div class="flex items-center justify-between gap20 flex-grow">
										<div class="name">
											<a href="product-list.html" class="body-text">Sojos Crunchy Natural Grain Free...</a>
										</div>
									</div>
								</li>
								<li class="mb-10">
									<div class="divider"></div>
								</li>
								<li class="product-item gap14 mb-10">
									<div class="image no-bg">
										<img src="images/products/21.png" alt="">
									</div>
									<div class="flex items-center justify-between gap20 flex-grow">
										<div class="name">
											<a href="product-list.html" class="body-text">Kristin Watson</a>
										</div>
									</div>
								</li>
								<li class="mb-10">
									<div class="divider"></div>
								</li>
								<li class="product-item gap14 mb-10">
									<div class="image no-bg">
										<img src="images/products/22.png" alt="">
									</div>
									<div class="flex items-center justify-between gap20 flex-grow">
										<div class="name">
											<a href="product-list.html" class="body-text">Mega Pumpkin Bone</a>
										</div>
									</div>
								</li>
								<li class="mb-10">
									<div class="divider"></div>
								</li>
								<li class="product-item gap14">
									<div class="image no-bg">
										<img src="images/products/23.png" alt="">
									</div>
									<div class="flex items-center justify-between gap20 flex-grow">
										<div class="name">
											<a href="product-list.html" class="body-text">Mega Pumpkin Bone</a>
										</div>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</form>
		</div>
		<div class="header-grid">

			<div class="header-item button-dark-light">
				<i class="icon-moon"></i>
			</div>


			<div class="header-item button-zoom-maximize">
				<div class="">
					<i class="icon-maximize"></i>
				</div>
			</div>

			<div class="popup-wrap user type-header">
				<div class="dropdown">
					<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
						<span class="header-user wg-user">
							<span class="image">
								<img src="{{ asset(auth()->user()->avatar ?? 'user.png') }}" alt="">
							</span>
							<span class="flex flex-column">
								<span class="body-title mb-2">{{ auth()->user()->name }}</span>
								<span class="text-tiny">Admin</span>
							</span>
						</span>
					</button>
					<ul class="dropdown-menu dropdown-menu-end has-content" aria-labelledby="dropdownMenuButton3" >
						<li>
							<a href="#" class="user-item">
								<div class="icon">
									<i class="icon-user"></i>
								</div>
								<div class="body-title-2">Account</div>
							</a>
						</li>
						<li>
							<a href="#" class="user-item">
								<div class="icon">
									<i class="icon-mail"></i>
								</div>
								<div class="body-title-2">Inbox</div>
								<div class="number">27</div>
							</a>
						</li>
						<li>
							<a href="#" class="user-item">
								<div class="icon">
									<i class="icon-file-text"></i>
								</div>
								<div class="body-title-2">Taskboard</div>
							</a>
						</li>
						<li>
							<a href="setting.html" class="user-item">
								<div class="icon">
									<i class="icon-settings"></i>
								</div>
								<div class="body-title-2">Setting</div>
							</a>
						</li>
						<li>
							<a href="#" class="user-item">
								<div class="icon">
									<i class="icon-headphones"></i>
								</div>
								<div class="body-title-2">Support</div>
							</a>
						</li>
						<li>
							<a class="user-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
								<div class="icon">
									<i class="icon-log-out"></i>
								</div>
								<div class="body-title-2">Log out</div>
							</a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

