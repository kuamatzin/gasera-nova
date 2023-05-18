import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-panel-esteroids', IndexField)
  app.component('detail-panel-esteroids', DetailField)
  app.component('form-panel-esteroids', FormField)
})
