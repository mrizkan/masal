<nav class="pcoded-navbar" pcoded-header-position="relative">
                    <div class="pcoded-inner-navbar">
                        <ul class="pcoded-item pcoded-left-item">
<!--                            <li class="pcoded-hasmenu">-->
<!--                                <a href="--><?php //= base_url('Home/dashboard') ?><!--">-->
<!--                                    <span class="pcoded-micon"><i class="icofont icofont-home"></i></span>-->
<!--                                    <span class="pcoded-mtext">Home</span>-->
<!--                                    <span class="pcoded-mcaret"></span>-->
<!--                                </a>-->
<!---->
<!--                            </li>-->

                            <li class="pcoded-hasmenu">
                                <a href="javascript:void(0)">
                                    <span class="pcoded-micon"><i class="icofont icofont-home"></i></span>
                                    <span class="pcoded-mtext">Home</span>
                                    <span class="pcoded-mcaret"></span>
                                </a>
                                <ul class="pcoded-submenu">
                                    <li class="">
                                        <a href="<?= base_url('/') ?>">
                                            <span class="pcoded-micon"></span>
                                            <span class="pcoded-mtext">Mark Attendance</span>
                                            <span class="pcoded-mcaret"></span>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="<?= base_url('Attendance/Delete_attendance') ?>">
                                            <span class="pcoded-micon"></span>
                                            <span class="pcoded-mtext">Delete Attendance</span>
                                            <span class="pcoded-mcaret"></span>
                                        </a>
                                    </li>

                                </ul>
                            </li>

                            <li class="pcoded-hasmenu" style="display: none;">
                                <a href="<?= base_url('Employee/AddEmployee') ?>">
                                    <span class="pcoded-micon"><i class="icofont icofont-brand-nexus"></i></span>
                                    <span class="pcoded-mtext">Add Employee</span>
                                    <span class="pcoded-mcaret"></span>
                                </a>

                            </li>
                            <li class="pcoded-hasmenu">
                                <a href="javascript:void(0)">
                                    <span class="pcoded-micon"><i class="fa fa-user"></i></span>
                                    <span class="pcoded-mtext">Employee</span>
                                    <span class="pcoded-mcaret"></span>
                                </a>
                                <ul class="pcoded-submenu">
                                    <li class="">
                                        <a href="<?= base_url('Employee/AddEmployee') ?>">
                                            <span class="pcoded-micon"></span>
                                            <span class="pcoded-mtext">Add Employee</span>
                                            <span class="pcoded-mcaret"></span>
                                        </a>
                                    </li>

                                </ul>
                            </li>

                            <li class="pcoded-hasmenu">
                                <a href="javascript:void(0)">
                                    <span class="pcoded-micon"><i class="fa fa-file-text-o"></i></span>
                                    <span class="pcoded-mtext">Report</span>
                                    <span class="pcoded-mcaret"></span>
                                </a>
                                <ul class="pcoded-submenu">
                                    <li class="">
                                        <a href="<?= base_url('Attendance/Reports') ?>">
                                            <span class="pcoded-micon"><i class="fa fa-file-text-o"></i></span>
                                            <span class="pcoded-mtext">Salary Report</span>

                                        </a>
                                    </li>
                                    <!--                                    <li class="">-->
                                    <!--                                        <a href="https://support.phoenixcoded.net/#/home" target="_blank" data-i18n="nav.submit-issue.main">-->
                                    <!--                                            <span class="pcoded-micon"><i class="ti-layout-list-post"></i></span>-->
                                    <!--                                            <span class="pcoded-mtext">Submit Issue</span>-->
                                    <!--                                            <span class="pcoded-mcaret"></span>-->
                                    <!--                                        </a>-->
                                    <!--                                    </li>-->
                                </ul>
                            </li>

<!--                            <li class="pcoded-hasmenu">-->
<!--                                <a href="--><?php //= base_url('Attendance/Reports') ?><!--">-->
<!--                                    <span class="pcoded-micon"><i class="fa fa-file-text-o"></i></span>-->
<!--                                    <span class="pcoded-mtext">Reports</span>-->
<!---->
<!--                                </a>-->
<!---->
<!--                            </li>-->
<!--                            <li class="pcoded-hasmenu">-->
<!--                                <a href="--><?php //= base_url('Attendance/Days') ?><!--">-->
<!--                                    <span class="pcoded-micon"><i class="fa fa-file-text-o"></i></span>-->
<!--                                    <span class="pcoded-mtext">Days</span>-->
<!---->
<!--                                </a>-->
<!---->
<!--                            </li>-->





                        </ul>
                    </div>
                </nav>