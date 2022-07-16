<template>
	<k-dialog
		ref="dialog"
		class="lbvs-create-error-dialog"
		:cancel-button="$t('close')"
		:submit-button="false"
	>
		<p class="lbvs-create-error-dialog-message">
			{{ error.message }}
		</p>

		<ul class="lbvs-create-error-dialog-list">
			<li v-for="(users, model) in error.details.lockedModels" :key="model">
				{{ model }}
				<span>({{ users.join(", ") }})</span>
			</li>
		</ul>
	</k-dialog>
</template>

<script>
export default {
	data() {
		return {
			error: {
				message: null,
				details: {
					lockedModels: {}
				}
			}
		};
	},
	methods: {
		open(error) {
			this.error = error;

			this.$refs.dialog.open();
		}
	}
};
</script>

<style>
.lbvs-create-error-dialog {
	line-height: 1.5;
}

.lbvs-create-error-dialog-message {
	margin-bottom: 1rem;

	color: var(--color-negative);
}

.lbvs-create-error-dialog-list li {
	margin-inline-start: 1.2rem;

	list-style: disc;
}

.lbvs-create-error-dialog-list span {
	color: var(--color-text-light);
}
</style>
