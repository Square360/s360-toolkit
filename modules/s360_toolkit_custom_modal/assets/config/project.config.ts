import * as path from 'path';

const PROJECT_CONFIG = {
  projectName: 's360-toolkit-custom-modal',
  pkg: require(path.resolve('package.json')),
  entryPoints: ['s360-toolkit-custom-modal.theme.js']
};

export { PROJECT_CONFIG };
