<template>
	<k-dialog
		ref="dialog"
		class="lbvs-version-dialog lbvs-version-delete-dialog"
		icon="trash"
		:submit-button="$t('versions.button.delete')"
		theme="negative"
		@submit="onSubmit"
	>
		<lbvs-version v-if="version" :version="version" />

		<p>{{ $t("versions.message.delete") }}</p>
	</k-dialog>
</template>

<script>
export default {
	data() {
		return {
			inProgress: false,
			version: null
		};
	},
	methods: {
		async onSubmit() {
			// prevent parallel execution
			if (this.inProgress === true) {
				return;
			}

			try {
				this.inProgress = true;

				await this.$store.dispatch({
					type: "versions/deleteVersion",
					version: this.version.name
				});

				this.$store.dispatch("notification/success", ":)");
				this.$refs.dialog.close();
			} catch (e) {
				this.$refs.dialog.error(e.message || e);
			} finally {
				this.inProgress = false;
			}
		},
		open(version) {
			this.version = version;

			this.$refs.dialog.open();
		}
	}
};
</script>

<style>
.lbvs-version-delete-dialog {
	line-height: 1.5;
}
</style>
