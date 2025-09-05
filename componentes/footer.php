
    <!-- MDBootstrap -->
        
       

        <!-- jQuery + DataTables + Extensions -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        
        <script src="../js/jquery.dataTables.min.js"></script>
        <script src="../js/dataTables.bootstrap5.min.js"></script>

        <!-- Extensiones de botones -->
        
            <script src="../js/dataTables.buttons.min.js"></script>
         <script src="../js/buttons.bootstrap5.min.js"></script>
        <script src="../js/jszip.min.js"></script>
        <script src="../js/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

        <!-- Responsive -->
        <script src="../js/dataTables.responsive.min.js"></script>
        <script src="../js/responsive.bootstrap5.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#tablaPacientes').DataTable({
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'copy',
                            text: '<i class="bi bi-clipboard-check me-1"></i> Copiar'
                        },
                        {
                            extend: 'csv',
                            text: '<i class="bi bi-filetype-csv me-1"></i> CSV'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF'
                        },
                        {
                            extend: 'print',
                            text: '<i class="bi bi-printer me-1"></i> Imprimir'
                        },
                        {
                            extend: 'colvis',
                            text: '<i class="bi bi-eye me-1"></i> Columnas'
                        }
                    ],
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                    }
                });
            });
        </script>

    <!-- Bootstrap JS y Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/mdb.min.js"></script>

    <!-- Chart.js -->
    <script src="../js/chart.js"></script>


</body>

</html>