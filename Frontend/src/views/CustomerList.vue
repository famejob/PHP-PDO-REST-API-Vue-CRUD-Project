<script setup>
import { RouterLink } from 'vue-router'
import { useCustomerStore } from '../stores/customer'
import { onMounted } from 'vue';
import $ from 'jquery';
import 'datatables.net'; // Import DataTables CSS
import 'datatables.net-bs5';
const customerStore = useCustomerStore()
onMounted(async () => {
  await customerStore.showCustomerList()
  $('#customerTable').DataTable();
});
const deleteCustomer = async (customerID) => {
  await customerStore.deleteCustomer(customerID)
  await customerStore.showCustomerList()
}
</script>

<template>
  <div class="container">
    <h1 class="text-center mt-4">Customer List</h1>
    <div class="d-flex justify-content-end mt-4">
      <RouterLink :to="{ name: 'customer_add' }">
        <button class=" btn btn-success">เพิ่มลูกค้า</button>
      </RouterLink>
    </div>
    <table id="customerTable" class="table mt-4">
      <thead>
        <tr>
          <th>ลำดับ</th>
          <th>ชื่อ-นามสกุล</th>
          <th>อีเมล</th>
          <th>เบอร์โทร</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(customer, index) in customerStore.customers" :key="customer.id">
          <td>{{ index + 1 }}</td>
          <td>{{ customer.first_name }}&nbsp; &nbsp; {{ customer.last_name }}</td>
          <td>{{ customer.email }}</td>
          <td>{{ customer.phone.replace(/-/g, '') }}</td>
          <td>
            <RouterLink :to="{ name: 'customer_edit', params: { id: customer.id } }">
              <button class="btn btn-warning">แก้ไขข้อมูล</button>
            </RouterLink>
            <RouterLink :to="{ name: 'customer_detail', params: { id: customer.id } }">
              <button class="btn btn-info ms-3">ดูข้อมูล</button>
            </RouterLink>
            <button @click="deleteCustomer(customer.id)" class="btn btn-danger ms-3">ลบข้อมูล</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
