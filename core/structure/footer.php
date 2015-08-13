
<?php if ( $this->body_class != 'boxed' ) { // Non-boxed layout, display normal footer ?>
			<?php if ( !$this->no_wrap ) echo '</div>'; ?>
		</section>
		</div>
		<footer id="footer">
			<div class="wrapper">
				<p class="left">&copy; MCPE Hub <?php echo date('Y'); ?> - Creations copyright of creators.</p>
				<p class="right">
					Part of the CubeMotion network.
					<span class="links">
						<a href="/tos">Terms</a> /
						<a href="/privacy">Privacy</a> /
						<a href="/links">Links</a>
					</span>
				</p>
			</div>
		</footer>
<?php } // END: Non-boxed layout, display normal footer ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="/assets/js/app.js"></script>
<?php foreach ( $this->scripts as $script ) { ?>
		<script src="/assets/js/<?php echo $script; ?>.js"></script>
<?php } ?>
	</body>
</html>