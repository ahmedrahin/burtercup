<div class="section-menu-left">
    <div class="box-logo">
        <a href="{{ route('dashboard') }}" id="site-logo-inner">
            <img class="" id="logo_header" alt="" src="{{ asset($system_settings->logo) }}" data-light="{{ asset($system_settings->logo) }}" data-dark="{{ asset($system_settings->dark_logo) }}" >
        </a>
        <div class="button-show-hide">
            <i class="icon-menu-left"></i>
        </div>
    </div>
    <div class="section-menu-left-wrap">
        <div class="center">
            <div class="center-item">
                <ul class="menu-list">
                    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="menu-item-button">
                            <div class="icon">
                                <i class="icon-grid"></i>
                            </div>
                            <div class="text">Dashboard</div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="center-item">
                <div class="center-heading">Admin Management</div>
                <ul class="menu-list">
                    <li class="menu-item has-children {{ request()->routeIs('admin*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-item-button">
                            <div class="icon"><i class="icon-user-check"></i></div>
                            <div class="text">Admin Details</div>
                        </a>
                        <ul class="sub-menu">
                            <li class="sub-menu-item">
                                <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
                                    <div class="text">Admin List</div>
                                </a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="{{ route('admin.create') }}" class="{{ request()->routeIs('admin.create') ? 'active' : '' }}">
                                    <div class="text">Create new Admin</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <div class="center-heading" style="margin-top: 15px;">User Management</div>
                    <ul class="menu-list">
                        <li class="menu-item">
                            <a href="{{ route('user.index') }}" class="{{ request()->routeIs('user.index') ? 'active' : '' }}" style="padding-bottom: 5px;">
                                <div class="icon"><i class="icon-user-plus"></i></div>
                                <div class="text">Register User</div>
                            </a>
                        </li>
                    </ul>

                    <ul class="menu-list">
                        <li class="menu-item">
                            <a href="{{ route('user.message') }}" class="{{ request()->routeIs('user.message') ? 'active' : '' }}">
                                <div class="icon"><i class="icon-mail"></i></div>
                                <div class="text">User Messages</div>
                            </a>
                        </li>
                    </ul>


                    <div class="center-heading" style="margin-top: 15px;">PRODUCT Management</div>
                     <ul class="menu-list">
                        <li class="menu-item">
                            <a href="{{ route('attribute.index') }}" class="{{ request()->routeIs('attribute*') ? 'active' : '' }}">
                                <div class="icon"><i class="icon-tag"></i></div>
                                <div class="text">Product Attribute</div>
                            </a>
                        </li>
                    </ul>

                     <li class="menu-item has-children {{ request()->routeIs('category*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-item-button">
                            <div class="icon"><i class="icon-layers"></i></div>
                            <div class="text">Categories</div>
                        </a>
                        <ul class="sub-menu">
                            <li class="sub-menu-item">
                                <a href="{{ route('category.index') }}" class="{{ request()->routeIs('category.index') ? 'active' : '' }}">
                                    <div class="text">All Category</div>
                                </a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="{{ route('category.create') }}" class="{{ request()->routeIs('category.create') ? 'active' : '' }}">
                                    <div class="text">Add new category</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item has-children {{ request()->routeIs('product*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-item-button">
                            <div class="icon"><i class="icon-gift"></i></div>
                            <div class="text">Products</div>
                        </a>
                        <ul class="sub-menu">
                            <li class="sub-menu-item">
                                <a href="{{ route('product.index') }}" class="{{ request()->routeIs('product.index') ? 'active' : '' }}">
                                    <div class="text">All Product</div>
                                </a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="{{ route('product.create') }}" class="{{ request()->routeIs('product.create') ? 'active' : '' }}">
                                    <div class="text">Add new product</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <div class="center-heading" style="margin-top: 15px;">Order Management</div>

                    <li class="menu-item has-children {{ request()->routeIs('order*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-item-button">
                            <div class="icon"><i class="icon-file-plus"></i></div>
                            <div class="text">Orders</div>
                        </a>
                        <ul class="sub-menu">
                            <li class="sub-menu-item">
                                <a href="{{ route('order.index') }}" class="{{ request()->routeIs('order.index') ? 'active' : '' }}">
                                    <div class="text">Order List</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                     <ul class="menu-list">
                        <li class="menu-item">
                            <a href="{{ route('delivery-option.index') }}" class="{{ request()->routeIs('delivery-option*') ? 'active' : '' }}">
                                <div class="icon"><i class="icon-tag"></i></div>
                                <div class="text">Delivery Option</div>
                            </a>
                        </li>
                    </ul>

                    <div class="center-heading" style="margin-top: 15px;">System Settings</div>
                    <li class="menu-item has-children {{ request()->routeIs('system-setting*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-item-button">
                            <div class="icon"><i class="icon-settings"></i></div>
                            <div class="text">Settings</div>
                        </a>
                        <ul class="sub-menu">
                            <li class="sub-menu-item">
                                <a href="{{ route('system-setting.index') }}" class="{{ request()->routeIs('system-setting.index') ? 'active' : '' }}">
                                    <div class="text">System Settings</div>
                                </a>
                            </li>
                            <li class="sub-menu-item">
                                <a href="{{ route('sociallink-setting.index') }}" class="{{ request()->routeIs('sociallink-setting.index') ? 'active' : '' }}">
                                    <div class="text">Social Link</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>

        </div>

    </div>
</div>
