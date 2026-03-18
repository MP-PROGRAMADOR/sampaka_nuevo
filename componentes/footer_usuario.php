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
        // Inicialización General para todas las tablas
        $('.table').each(function() {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    responsive: true,
                    dom: '<"d-flex justify-content-between mb-3"Bf>rtip',
                    buttons: [{
                            extend: 'copy',
                            text: '<i class="bi bi-clipboard-check"></i>',
                            titleAttr: 'Copiar a portapapeles',
                            className: 'btn btn-primary btn-sm px-3 shadow-0'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="bi bi-file-earmark-excel"></i>',
                            titleAttr: 'Exportar a Excel',
                            className: 'btn btn-success btn-sm px-3 shadow-0'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="bi bi-file-earmark-pdf"></i>',
                            titleAttr: 'Exportar a PDF',
                            className: 'btn btn-danger btn-sm px-3 shadow-0'
                        },
                        {
                            extend: 'print',
                            text: '<i class="bi bi-printer"></i>',
                            titleAttr: 'Imprimir tabla',
                            className: 'btn btn-info btn-sm px-3 shadow-0'
                        }
                    ],
                    // TRADUCCIÓN DIRECTA AQUÍ:
                    language: {
                        "processing": "Procesando...",
                        "lengthMenu": "Mostrar _MENU_ registros",
                        "zeroRecords": "No se encontraron resultados",
                        "emptyTable": "Ningún dato disponible en esta tabla",
                        "info": "Mostrando _START_ de un total de _TOTAL_ registros",
                        "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                        "search": "Buscar:",
                        "infoThousands": ",",
                        "loadingRecords": "Cargando...",
                        "paginate": {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        },
                        "aria": {
                            "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                            "sortDescending": ": Activar para ordenar la columna de manera descendente"
                        },
                        "buttons": {
                            "copy": "Copiar",
                            "colvis": "Visibilidad",
                            "collection": "Colección",
                            "colvisRestore": "Restaurar visibilidad",
                            "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione Escape.",
                            "copySuccess": {
                                "1": "Copiada 1 fila al portapapeles",
                                "_": "Copiadas %d fila al portapapeles"
                            },
                            "copyTitle": "Copiar al portapapeles",
                            "csv": "CSV",
                            "excel": "Excel",
                            "pageLength": {
                                "-1": "Mostrar todas las filas",
                                "_": "Mostrar %d filas"
                            },
                            "pdf": "PDF",
                            "print": "Imprimir"
                        }
                    }
                });
            }
        });

        // Gestión de alertas
        setTimeout(() => {
            $('.fade-msg').fadeOut(1000, function() {
                $(this).remove();
            });
        }, 5000);
    });
</script>




</body>

</html>