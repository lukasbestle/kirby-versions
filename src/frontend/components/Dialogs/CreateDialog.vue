<template>
	<div>
		<k-dialog
			ref="dialog"
			size="large"
			:submit-button="$t('versions.button.create')"
			theme="positive"
			@submit="onSubmit"
		>
			<k-form ref="form" :fields="fields" @submit="onSubmit">
				<template #header>
					<k-field
						:label="$t('versions.label.changes')"
						class="lbvs-create-changes"
					>
						<lbvs-stages :to-stage="toStage" />
					</k-field>
				</template>
			</k-form>
		</k-dialog>

		<lbvs-create-error-dialog ref="errorDialog" />
	</div>
</template>

<script>
export default {
	data() {
		return {
			instance: null,
			inProgress: false,
			toStage: {}
		};
	},
	computed: {
		fields() {
			return {
				label: {
					autofocus: true,
					icon: "title",
					label: this.$t("versions.label.label"),
					type: "text"
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

				let label = this.$refs.form.value.label;
				if (!label) {
					throw this.$t("field.required");
				}

				// stage list of changes
				await this.$store.dispatch({
					type: "versions/addChangesToStage",
					changes: this.toStage
				});

				await this.$store.dispatch({
					type: "versions/createVersion",
					instance: this.instance,
					label: label
				});

				this.$store.dispatch("notification/success", ":)");
				this.$refs.dialog.close();
			} catch (e) {
				this.$refs.dialog.error(e.message || e);
			} finally {
				this.inProgress = false;
			}
		},
		open(instance, toStage) {
			this.instance = instance;
			this.toStage = toStage;

			this.$refs.dialog.open();
		}
	}
};
</script>

<style>
.lbvs-create-changes {
	margin-bottom: 2.25rem;
}
</style>
