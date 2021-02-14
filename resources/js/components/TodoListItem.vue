<template>
    <div class="list-group-item">


        <div v-if="!is_edit" class="d-flex w-100 justify-content-between">
            <div class="form-check w-75">
                <input class="form-check-input " type="checkbox" value="" :id="id" v-model="item.is_completed" @change="updateCompleted">
                <label :for="id" :class="['form-check-label', {'text-strikethrough' : !!item.is_completed && !is_saving}]">
                    {{ item.description }}
                </label>
            </div>
            <div class="ml-1 w-25">
                <div class="btn-toolbar float-right" role="toolbar">
                    <div class="btn-group mr-lg-1" role="group">
                        <button type="button" class="btn btn-primary" :disabled="!!item.is_completed" @click="showUpdate">
                            <font-awesome-icon icon="edit" />
                        </button>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-danger" @click.prevent="removeItem">
                            <font-awesome-icon icon="trash-alt" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="d-flex w-100 justify-content-between">
            <form class="w-75" @submit.prevent="updateDescription">
                <div class="form-group">
                    <input type="text" class="form-control" v-model="description"
                           @keyup.esc="hideUpdate">
                </div>
            </form>
            <div class="ml-1 w-25">
                <div class="btn-toolbar float-right" role="toolbar">
                    <div class="btn-group mr-lg-1" role="group">
                        <button type="button" class="btn btn-success" @click.prevent="updateDescription">
                            <font-awesome-icon icon="save" size="lg"/>
                        </button>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-danger" @click.prevent="hideUpdate">
                            <font-awesome-icon icon="times" size="lg"/>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "TodoListItem",
    props: ['item', 'index'],
    data() {
        return {
            is_saving: 0,
            is_completed: false,
            is_edit: 0,
            description: null,
        }
    },
    methods: {
        async removeItem() {
            try {
                let response = await axios.delete(`api/tasks/${this.item.id}`);

                console.log(response);

                this.$emit('itemdeleted', response.data.data, this.index);

            } catch (e) {
                console.log(e);
            }
        },
        async updateCompleted() {

            if( this.is_saving) return;

            this.is_saving = 1;

            try {
                // TODO: create endpoint just for the toggle of completed
                let response = await axios.put(`api/tasks/${this.item.id}`, this.item);

                this.$emit('itemupdated', response.data.data, this.index);

            } catch (e) {

            } finally {
                this.is_saving = 0;
            }
        },
        showUpdate() {
            this.description = this.item.description;
            this.is_edit = 1;
        },
        async updateDescription() {
            try {
                let response = await axios.put(`api/tasks/${this.item.id}`, {...this.item, description: this.description});
                this.$emit('itemupdated', response.data.data, this.index);
                this.hideUpdate();
            } catch (e) {
                console.log(e);
            }
        },
        hideUpdate() {
            this.is_edit = 0;
            this.description = null;
        },
    },
    computed: {
        id() {
            return "item-" + this.item.id;
        }
    }
}
</script>

<style scoped>
    .text-strikethrough {
        text-decoration: line-through !important;
    }
</style>
