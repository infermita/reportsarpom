@if( request()->path()!='login' )
                    </div>
                </div>       
            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Geco 2023</span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
@endif
</body>
</html>
<script>
    $("#sidebarToggle").on("click", function () {
        if ($('#page-top').attr('class') == "sidebar-toggled") {
            $("#accordionSidebar").removeClass('toggled');
            $("#page-top").removeClass('sidebar-toggled');
        }else {
            $("#accordionSidebar").addClass('toggled');
            $("#page-top").addClass('sidebar-toggled');}
	});
</script>   

