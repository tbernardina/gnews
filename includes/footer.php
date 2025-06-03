</main>

    <footer class="site-footer" role="contentinfo">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Portal de Notícias Acessível. Todos os direitos reservados.</p>
            <p><a href="#top" class="back-to-top" aria-label="Voltar ao topo da página">Voltar ao Topo &uarr;</a></p>
            </div>
    </footer>

    <script src="assets/js/script.js"></script> 
</body>
</html>
<?php
if (isset($conn)) {
    $conn->close(); // Fecha a conexão com o BD se estiver aberta
}
?>