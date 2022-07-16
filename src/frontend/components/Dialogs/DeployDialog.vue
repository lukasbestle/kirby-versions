<template>
	<k-dialog
		ref="dialog"
		class="lbvs-version-dialog"
		:submit-button="$t('versions.button.deploy')"
		theme="positive"
		@submit="onSubmit"
	>
		<lbvs-version v-if="version" :version="version" />

		<k-form ref="form" :fields="fields" @submit="onSubmit" />
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
	computed: {
		fields() {
			let currentInstance = this.$store.getters["versions/currentInstance"];
			let options = Object.values(
				this.$store.state.versions.data.instances
			).map((instance) => ({ text: instance.name, value: instance.name }));

			return {
				instance: {
					disabled: options.length <= 1,
					empty: false,
					icon: "box",
					label: this.$t("versions.label.targetInstance"),
					options: options,
					placeholder: currentInstance.name,
					type: "select",
					value: currentInstance.name
				}
			};
		}
	},
	methods: {
		async onSubmit() {
			// prevent parallel execution
			if (this.inProgress === true) {
				return;
			}

			try {
				this.inProgress = true;

				// fallback to the default if the user didn't
				// explicitly select an option
				let instance =
					this.$refs.form.value.instance ||
					this.$store.getters["versions/currentInstance"].name;

				await this.$store.dispatch({
					type: "versions/deployVersion",
					version: this.version.name,
					instance: instance
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
