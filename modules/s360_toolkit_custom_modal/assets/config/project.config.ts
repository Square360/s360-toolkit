import * as path from 'path';

const PROJECT_CONFIG = {
  projectName: 's360-toolkit-custom-modal',
  pkg: require(path.resolve('package.json')),
  entryPoints: ['s360-toolkit-custom-modal.theme.js','s360-toolkit-custom-modal.admin.js']
};

export { PROJECT_CONFIG };
