import Dropzone from 'react-dropzone'

type PropTypes = {
  onChange: (files: File[]) => void,
  children: React.ReactNode
}

const Droppable = ({ onChange, children }: PropTypes) => {
  return (
    <Dropzone onDrop={onChange}>
      {({ getInputProps }) => (
        <>
          <input {...getInputProps()} />
          {children}
        </>
      )}
    </Dropzone>
  )
}

export default Droppable
