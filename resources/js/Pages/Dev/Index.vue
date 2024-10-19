<script setup>
import {ref} from "vue";
import "codemirror/mode/sql/sql.js"
import {Head} from "@inertiajs/vue3";
import Codemirror from "codemirror-editor-vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const sql = ref("");

const cmOptions = {
  mode: "text/x-sql",
  lint: true,
  gutters: ["CodeMirror-lint-markers"],
};

const onChange = (val, cm) => {
  console.log(val);
  console.log(cm.getValue());
};

const onInput = (val) => {
  console.log(val);
};

const onReady = (cm) => {
  console.log(cm.focus());
};

</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">SQL Executor</h2>
    </template>
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="grid grid-cols-1 divide-y">
            <div class="mb-2">
              <el-row>
                <el-col :span="24">
                  <Codemirror class="sql-code font-sans rounded border-slate-100" v-model:value="sql"
                              :options="cmOptions" border ref="cmRef" height="200"
                              @change="onChange" @input="onInput" @ready="onReady"
                  >
                  </Codemirror>
                </el-col>
              </el-row>
            </div>
            <div class="mb-4 pt-4">
              <el-row :gutter="20">
                <el-col :span="12"></el-col>
                <el-col :span="12" class="text-right">
                  <el-button size="large" plain>Format</el-button>
                  <el-button type="primary" size="large" plain>Execute</el-button>
                </el-col>
              </el-row>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.sql-code {
  font-size: 14px;
  font-family: monospace;
}
</style>