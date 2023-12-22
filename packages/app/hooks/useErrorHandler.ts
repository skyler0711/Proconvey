import { CombinedError } from 'urql'

const useErrorHandler = () => {
  return async (error: CombinedError, setError?: Function, specificIndex?: number) => {
    if (error.graphQLErrors) {
      const fields: string[] = []
      const replaceValue = specificIndex !== undefined
        ? `value.${specificIndex}`
        : null

      error.graphQLErrors.forEach((error) => {
        if (error.extensions.validation) {

          Object.entries(error.extensions.validation).forEach(([field, messages]) => {
            let name = field.substring(field.indexOf('.') + 1)

            if (specificIndex !== undefined) {
              name = name.replace(replaceValue!, 'value')
            }

            if (!fields.includes(name)) {
              setError?.call(null, name, { message: messages[0] })
              fields.push(name)
            }
          })
        }
      })
    } else {
      throw error
    }
  }
}

export default useErrorHandler
