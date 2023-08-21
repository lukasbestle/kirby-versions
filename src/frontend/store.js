export default (app) => ({
	namespaced: true,
	state: {
		data: {
			markedChangesForStaging: {},
			instances: {},
			versions: {}
		}
	},
	getters: {
		/**
		 * @returns {object} The instance marked with `isCurrent: true`
		 */
		currentInstance(state) {
			return Object.values(state.data.instances).find(
				(instance) => instance.isCurrent
			);
		},

		currentChanges(state) {
			return state.data.markedChangesForStaging;
		}
	},
	mutations: {
		SET_DATA(state, { instances, versions }) {
			state.data.instances = instances;
			state.data.versions = versions;
		},

		SET_CHANGES(state, { path, status }) {
			state.data.markedChangesForStaging[path] = status;
		},

		REMOVE_CHANGES(state, { path }) {
			delete state.data.markedChangesForStaging[path];
		}
	},
	actions: {
		/**
		 * Initialize the plugin data from the API
		 */
		async load({ commit }) {
			commit("SET_DATA", await app.$api.get("versions"));
		},

		/**
		 * Prepare version creation
		 * Stages all changes and validates that the version
		 * can be created based on the current set of changes
		 *
		 * @param {string} instance Name of the instance to create the version from
		 * @returns {object} List of staged changes
		 */
		async prepareVersionCreation(context, { instance }) {
			return await app.$api.post("versions/prepareCreation", { instance });
		},

		/**
		 * Stage changes
		 * Stage changes for version creation
		 *
		 * @param {object} changes List of changes to stage
		 */
		async addChangesToStage({ commit }, { changes }) {
			await app.$api.post("versions/addChanges", { changes });
		},

		/**
		 * Create version
		 * Commits the previously prepared version
		 *
		 * @param {string} instance Name of the instance to create the version from
		 * @param {string} label Custom version label
		 */
		async createVersion({ commit }, { instance, label }) {
			let data = await app.$api.post("versions/create", { instance, label });
			commit("SET_DATA", data);
		},

		/**
		 * Delete version
		 * Deletes a version's Git tag
		 *
		 * @param {string} version Unique version name
		 */
		async deleteVersion({ commit }, { version }) {
			let data = await app.$api.delete("versions/versions/" + version);
			commit("SET_DATA", data);
		},

		/**
		 * Deploy version
		 * Deploys a version to a specified instance
		 *
		 * @param {string} instance Name of the instance to deploy to
		 * @param {string} version Unique version name
		 */
		async deployVersion({ commit }, { instance, version }) {
			let data = await app.$api.post(
				"versions/versions/" + version + "/deploy",
				{ instance }
			);
			commit("SET_DATA", data);
		},

		/**
		 * Export version
		 * Returns the URL to a ZIP file of the given version
		 *
		 * @param {string} version Unique version name
		 * @returns {object} ZIP `url`, version `name` and `label`, `filesize`
		 */
		async exportVersion(context, { version }) {
			return await app.$api.post("versions/versions/" + version + "/export");
		},

		setChanges({ commit }, { path, status }) {
			if (status === false) {
				commit("REMOVE_CHANGES", { path });
				return;
			}
			return commit("SET_CHANGES", { path, status });
		}
	}
});
