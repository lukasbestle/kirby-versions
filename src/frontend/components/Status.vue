<template>
	<div class="lbvs-status">
		<k-view>
			<k-grid>
				<k-column width="1/3">
					<header class="k-section-header">
						<k-label type="section">
							{{ $t("versions.label.instances") }}
						</k-label>
					</header>

					<ul class="lbvs-status-instances">
						<li
							v-for="instance in $store.state.versions.data.instances"
							:key="instance.name"
							:class="{ current: instance.isCurrent }"
						>
							<lbvs-instance-name :instance="instance" />

							<span v-if="instance.isCurrent" class="lbvs-status-current">
								{{ $t("versions.label.current") }}
							</span>

							<lbvs-version
								v-if="instance.version"
								:version="{
									name: instance.version,
									label: instance.versionLabel
								}"
							/>
						</li>
					</ul>
				</k-column>

				<k-column width="2/3" class="lbvs-status-changes">
					<header class="k-section-header">
						<k-label type="section">
							{{ $t("versions.label.changes") }}
						</k-label>

						<k-button
							v-if="canCheck"
							icon="undo"
							size="xs"
							:disabled="canCheck === false"
							@click="onUncheck"
						>
							{{ $t("versions.button.uncheckall") }}
						</k-button>

						<k-button
							v-if="canUncheck"
							icon="wand"
							size="xs"
							:disabled="canUncheck === false"
							@click="onCheckall"
						>
							{{ $t("versions.button.checkall") }}
						</k-button>

						<k-button
							icon="add"
							size="xs"
							:disabled="canCreateVersion === false"
							@click="onCreate"
						>
							{{ $t("versions.button.create") }}
						</k-button>
					</header>

					<lbvs-changes :changes="currentChanges" />
				</k-column>
			</k-grid>
		</k-view>

		<lbvs-create-dialog ref="createDialog" />
	</div>
</template>

<script>
export default {
	emits: ['uncheck'],
	computed: {
		canCreateVersion() {
			// user must have the create permission and
			// there needs to be at least one change to commit
			return (
				this.$permissions["lukasbestle.versions"].create === true &&
				Object.keys(this.markedChanges).length > 0
			);
		},
		canCheck() {
			// user must have the create permission and
			// there needs to be at least one change to commit
			return (
				this.$permissions["lukasbestle.versions"].create === true &&
				Object.keys(this.markedChanges).length > 0
			);
		},
		canUncheck() {
			// user must have the create permission and
			// there needs to be at least one change to commit
			return (
				this.$permissions["lukasbestle.versions"].create === true &&
				!Object.keys(this.markedChanges).length > 0
			);
		},
		currentChanges() {
			return this.$store.getters["versions/currentInstance"].changes;
		},
		markedChanges() {
			return this.$store.getters["versions/currentChanges"];
		}
	},
	methods: {
		onUncheck() {
			this.$store.dispatch("versions/uncheckAll");
		},
		onCheckall() {
			this.$store.dispatch("versions/checkAll");
		},
		onCreate() {
			let instance = this.$store.getters["versions/currentInstance"].name;
			const toStage = this.$store.getters["versions/currentChanges"];
			return this.$refs.createDialog.open(instance, toStage);
		}
	}
};
</script>

<style>
.lbvs-status {
	padding-top: 1.5rem;
	padding-bottom: 2rem;

	background: #2b2b2b;
	color: var(--color-white);
	border-radius: var(--rounded);
}

.lbvs-status .k-grid {
	/* gap between the columns on mobile */
	grid-row-gap: 1.5rem;
}

.lbvs-status-instances li {
	padding: 0.8rem;
}
.lbvs-status-instances li.current {
	background: var(--color-background);
	border-radius: var(--rounded);
	color: var(--color-text);
}

.lbvs-status-current {
	display: inline-block;
	margin-inline-start: 0.5rem;
	padding: 0.1em 0.3em;

	border: 1px solid var(--color-border);
	border-radius: var(--rounded-sm);

	font-size: var(--font-size-small);
}

.lbvs-status-instances .lbvs-version {
	margin-top: 0.4rem;
}

.lbvs-status-changes {
	/* ensure the list of changes fills the whole view vertically */
	display: flex;
	flex-direction: column;
	height: 100%;
}

.lbvs-status-changes .lbvs-changes {
	height: 100%;
	padding: 0.8rem;

	background: var(--color-background);
	border-radius: var(--rounded);
	color: var(--color-text);
}

/* no rounded corners between instances and changes columns,
   unless the columns are displayed vertically (on mobile) */
@media screen and (min-width: 30em) {
	.lbvs-status-instances li.current {
		border-start-end-radius: 0;
		border-end-end-radius: 0;
	}

	.lbvs-status-changes .lbvs-changes {
		border-start-start-radius: 0;
		border-end-start-radius: 0;
	}
}
</style>
