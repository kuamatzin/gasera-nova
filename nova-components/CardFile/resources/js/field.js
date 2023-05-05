import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-card-file', IndexField)
  app.component('detail-card-file', DetailField)
  app.component('form-card-file', FormField)
})
